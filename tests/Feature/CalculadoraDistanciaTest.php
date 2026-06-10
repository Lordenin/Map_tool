<?php

namespace Tests\Feature;

use App\Models\Analise;
use App\Models\Ponto;
use App\Services\CalculadoraDistancia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculadoraDistanciaTest extends TestCase
{
    use RefreshDatabase;

    public function test_haversine_calcula_distancia_conhecida(): void
    {
        $calc = new CalculadoraDistancia();

        // 1 grau de latitude ≈ 111.195 m (arco com raio de 6.371.000 m).
        $distancia = $calc->haversine(0.0, 0.0, 1.0, 0.0);

        $this->assertEqualsWithDelta(111195, $distancia, 5);
    }

    public function test_haversine_retorna_zero_para_pontos_coincidentes(): void
    {
        $calc = new CalculadoraDistancia();

        // Sem a proteção contra NaN, pontos idênticos quebrariam o sqrt.
        $this->assertSame(0, $calc->haversine(-23.5505, -46.6333, -23.5505, -46.6333));
    }

    public function test_recalcular_analise_persiste_distancia_dos_concorrentes(): void
    {
        $analise = Analise::factory()->create();

        Ponto::factory()->estabelecimento()->create([
            'analise_id' => $analise->id,
            'latitude' => -23.5505,
            'longitude' => -46.6333,
        ]);

        $concorrente = Ponto::factory()->concorrente()->create([
            'analise_id' => $analise->id,
            'latitude' => -23.5600,
            'longitude' => -46.6400,
            'distancia_metros' => null,
        ]);

        (new CalculadoraDistancia())->recalcularAnalise($analise);

        $esperado = (new CalculadoraDistancia())
            ->haversine(-23.5505, -46.6333, -23.5600, -46.6400);

        $this->assertSame($esperado, $concorrente->refresh()->distancia_metros);
        $this->assertGreaterThan(0, $concorrente->distancia_metros);
    }

    public function test_recalcular_analise_sem_estabelecimento_nao_quebra(): void
    {
        $analise = Analise::factory()->create();

        $concorrente = Ponto::factory()->concorrente()->create([
            'analise_id' => $analise->id,
            'distancia_metros' => null,
        ]);

        (new CalculadoraDistancia())->recalcularAnalise($analise);

        // Sem ponto de referência, a distância permanece nula (sem erro).
        $this->assertNull($concorrente->refresh()->distancia_metros);
    }
}

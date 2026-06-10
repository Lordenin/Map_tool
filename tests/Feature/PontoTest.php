<?php

namespace Tests\Feature;

use App\Models\Analise;
use App\Models\Ponto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PontoTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_e_redirecionado_para_login(): void
    {
        $analise = Analise::factory()->create();

        $this->get(route('analises.pontos.create', $analise))->assertRedirect('/login');
    }

    public function test_pagina_de_criacao_do_estabelecimento_e_exibida(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();

        $this->actingAs($user)
            ->get(route('analises.pontos.create', ['analise' => $analise, 'tipo' => 'estabelecimento']))
            ->assertOk()
            ->assertSee('Definir estabelecimento');
    }

    public function test_estabelecimento_pode_ser_definido(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();

        $response = $this->actingAs($user)->post(route('analises.pontos.store', $analise), [
            'tipo' => Ponto::TIPO_ESTABELECIMENTO,
            'nome' => 'Loja do Cliente',
            'endereco' => 'Av. Paulista, 1000',
            'latitude' => '-23.5505',
            'longitude' => '-46.6333',
        ]);

        $response->assertRedirect(route('analises.show', $analise));
        $this->assertDatabaseHas('pontos', [
            'analise_id' => $analise->id,
            'tipo' => Ponto::TIPO_ESTABELECIMENTO,
            'nome' => 'Loja do Cliente',
        ]);
    }

    public function test_analise_nao_aceita_dois_estabelecimentos(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();
        Ponto::factory()->estabelecimento()->create(['analise_id' => $analise->id]);

        $response = $this->actingAs($user)
            ->from(route('analises.show', $analise))
            ->post(route('analises.pontos.store', $analise), [
                'tipo' => Ponto::TIPO_ESTABELECIMENTO,
                'nome' => 'Segundo estabelecimento',
                'latitude' => '-23.5',
                'longitude' => '-46.6',
            ]);

        $response->assertSessionHasErrors('tipo');
        $this->assertSame(1, $analise->pontos()->where('tipo', Ponto::TIPO_ESTABELECIMENTO)->count());
    }

    public function test_concorrente_recebe_distancia_quando_ha_estabelecimento(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();
        Ponto::factory()->estabelecimento()->create([
            'analise_id' => $analise->id,
            'latitude' => -23.5505,
            'longitude' => -46.6333,
        ]);

        $this->actingAs($user)->post(route('analises.pontos.store', $analise), [
            'tipo' => Ponto::TIPO_CONCORRENTE,
            'nome' => 'Concorrente A',
            'latitude' => '-23.5600',
            'longitude' => '-46.6400',
        ]);

        $concorrente = Ponto::firstWhere('nome', 'Concorrente A');

        $this->assertNotNull($concorrente->distancia_metros);
        $this->assertGreaterThan(0, $concorrente->distancia_metros);
    }

    public function test_concorrente_sem_estabelecimento_fica_sem_distancia(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();

        $this->actingAs($user)->post(route('analises.pontos.store', $analise), [
            'tipo' => Ponto::TIPO_CONCORRENTE,
            'nome' => 'Concorrente sem ref',
            'latitude' => '-23.56',
            'longitude' => '-46.64',
        ]);

        $concorrente = Ponto::firstWhere('nome', 'Concorrente sem ref');

        $this->assertNull($concorrente->distancia_metros);
    }

    public function test_coordenadas_sao_obrigatorias(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('analises.pontos.create', $analise))
            ->post(route('analises.pontos.store', $analise), [
                'tipo' => Ponto::TIPO_CONCORRENTE,
                'nome' => 'Sem coordenadas',
                'latitude' => '',
                'longitude' => '',
            ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
        $this->assertDatabaseCount('pontos', 0);
    }

    public function test_latitude_fora_do_intervalo_e_rejeitada(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('analises.pontos.create', $analise))
            ->post(route('analises.pontos.store', $analise), [
                'tipo' => Ponto::TIPO_CONCORRENTE,
                'nome' => 'Latitude inválida',
                'latitude' => '120',
                'longitude' => '-46.6',
            ]);

        $response->assertSessionHasErrors('latitude');
    }

    public function test_mover_estabelecimento_recalcula_distancia_do_concorrente(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();
        $estabelecimento = Ponto::factory()->estabelecimento()->create([
            'analise_id' => $analise->id,
            'latitude' => -23.5505,
            'longitude' => -46.6333,
        ]);
        $concorrente = Ponto::factory()->concorrente()->create([
            'analise_id' => $analise->id,
            'latitude' => -23.5600,
            'longitude' => -46.6400,
            'distancia_metros' => 1, // valor obsoleto qualquer
        ]);

        $this->actingAs($user)->put(route('pontos.update', $estabelecimento), [
            'tipo' => Ponto::TIPO_ESTABELECIMENTO,
            'nome' => $estabelecimento->nome,
            'latitude' => '-23.5900', // move o estabelecimento para perto do concorrente
            'longitude' => '-46.6400',
        ]);

        $novaDistancia = $concorrente->refresh()->distancia_metros;

        $this->assertNotSame(1, $novaDistancia);
        $this->assertGreaterThan(0, $novaDistancia);
    }

    public function test_remover_estabelecimento_zera_distancia_dos_concorrentes(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();
        $estabelecimento = Ponto::factory()->estabelecimento()->create(['analise_id' => $analise->id]);
        $concorrente = Ponto::factory()->concorrente()->create([
            'analise_id' => $analise->id,
            'distancia_metros' => 500,
        ]);

        $this->actingAs($user)
            ->delete(route('pontos.destroy', $estabelecimento))
            ->assertRedirect(route('analises.show', $analise));

        $this->assertDatabaseMissing('pontos', ['id' => $estabelecimento->id]);
        $this->assertNull($concorrente->refresh()->distancia_metros);
    }

    public function test_concorrente_pode_ser_removido(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();
        $concorrente = Ponto::factory()->concorrente()->create(['analise_id' => $analise->id]);

        $this->actingAs($user)
            ->delete(route('pontos.destroy', $concorrente))
            ->assertRedirect(route('analises.show', $analise));

        $this->assertDatabaseMissing('pontos', ['id' => $concorrente->id]);
    }

    public function test_pontos_aparecem_no_show_da_analise(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();
        Ponto::factory()->estabelecimento()->create([
            'analise_id' => $analise->id,
            'nome' => 'Matriz do Cliente',
        ]);
        Ponto::factory()->concorrente()->create([
            'analise_id' => $analise->id,
            'nome' => 'Rival da Esquina',
        ]);

        $this->actingAs($user)
            ->get(route('analises.show', $analise))
            ->assertOk()
            ->assertSee('Matriz do Cliente')
            ->assertSee('Rival da Esquina');
    }
}

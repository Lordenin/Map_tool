<?php

namespace Database\Factories;

use App\Models\Analise;
use App\Models\Ponto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Ponto>
 */
class PontoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'analise_id' => Analise::factory(),
            'tipo' => Ponto::TIPO_CONCORRENTE,
            'nome' => fake()->company(),
            'endereco' => fake()->streetAddress(),
            // Região metropolitana de São Paulo (coordenadas plausíveis para testes).
            'latitude' => fake()->latitude(-23.7, -23.5),
            'longitude' => fake()->longitude(-46.7, -46.5),
            'observacoes' => fake()->optional()->sentence(),
            'distancia_metros' => null,
        ];
    }

    public function estabelecimento(): static
    {
        return $this->state(fn () => ['tipo' => Ponto::TIPO_ESTABELECIMENTO]);
    }

    public function concorrente(): static
    {
        return $this->state(fn () => ['tipo' => Ponto::TIPO_CONCORRENTE]);
    }
}

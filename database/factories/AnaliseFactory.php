<?php

namespace Database\Factories;

use App\Models\Analise;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Analise>
 */
class AnaliseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'titulo' => fake()->sentence(3),
            'descricao' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(array_keys(Analise::STATUSES)),
            'data_analise' => fake()->optional()->date(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nome' => fake()->company(),
            'segmento' => fake()->randomElement([
                'Varejo', 'Alimentação', 'Serviços', 'Saúde', 'Educação', 'Beleza',
            ]),
            'observacoes' => fake()->optional()->sentence(),
        ];
    }
}

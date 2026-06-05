<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Cria as contas fixas dos sócios. Não há registro público na ferramenta.
 *
 * A senha inicial vem de SEED_SOCIO_PASSWORD (.env) para não ficar versionada.
 * É idempotente: rodar de novo não duplica contas nem reseta senhas já trocadas
 * de quem já existe — apenas garante que cada sócio listado exista.
 */
class SociosSeeder extends Seeder
{
    public function run(): void
    {
        $socios = [
            ['name' => 'Duilso', 'email' => 'edieworm@gmail.com'],
            // Adicione os demais sócios aqui (name + email).
        ];

        $senhaPadrao = env('SEED_SOCIO_PASSWORD', 'MudarEsta@123');

        foreach ($socios as $socio) {
            User::firstOrCreate(
                ['email' => $socio['email']],
                [
                    'name' => $socio['name'],
                    'password' => Hash::make($senhaPadrao),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Analise;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnaliseTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_e_redirecionado_para_login(): void
    {
        $cliente = Cliente::factory()->create();

        $this->get(route('clientes.analises.create', $cliente))->assertRedirect('/login');
    }

    public function test_pagina_de_criacao_e_exibida(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['nome' => 'Padaria do Zé']);

        $this->actingAs($user)
            ->get(route('clientes.analises.create', $cliente))
            ->assertOk()
            ->assertSee('Padaria do Zé');
    }

    public function test_analise_pode_ser_criada_e_vinculada_ao_cliente(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($user)->post(route('clientes.analises.store', $cliente), [
            'titulo' => 'Mapeamento da concorrência - Centro',
            'descricao' => 'Raio de 1km a partir da loja.',
            'status' => Analise::STATUS_EM_ANDAMENTO,
            'data_analise' => '2026-06-09',
        ]);

        $analise = Analise::firstWhere('titulo', 'Mapeamento da concorrência - Centro');

        $this->assertNotNull($analise);
        $this->assertSame($cliente->id, $analise->cliente_id);
        $this->assertSame(Analise::STATUS_EM_ANDAMENTO, $analise->status);
        $response->assertRedirect(route('analises.show', $analise));
    }

    public function test_titulo_e_obrigatorio_no_cadastro(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('clientes.analises.create', $cliente))
            ->post(route('clientes.analises.store', $cliente), [
                'titulo' => '',
                'status' => Analise::STATUS_RASCUNHO,
            ]);

        $response->assertSessionHasErrors('titulo')
            ->assertRedirect(route('clientes.analises.create', $cliente));
        $this->assertDatabaseCount('analises', 0);
    }

    public function test_status_invalido_e_rejeitado(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('clientes.analises.create', $cliente))
            ->post(route('clientes.analises.store', $cliente), [
                'titulo' => 'Análise qualquer',
                'status' => 'status_que_nao_existe',
            ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseCount('analises', 0);
    }

    public function test_detalhe_da_analise_e_exibido(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create(['titulo' => 'Análise do Bairro Sul']);

        $this->actingAs($user)
            ->get(route('analises.show', $analise))
            ->assertOk()
            ->assertSee('Análise do Bairro Sul');
    }

    public function test_pagina_de_edicao_e_exibida(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create(['titulo' => 'Título Antigo']);

        $this->actingAs($user)
            ->get(route('analises.edit', $analise))
            ->assertOk()
            ->assertSee('Título Antigo');
    }

    public function test_analise_pode_ser_atualizada(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create([
            'titulo' => 'Título Antigo',
            'status' => Analise::STATUS_RASCUNHO,
        ]);

        $response = $this->actingAs($user)->put(route('analises.update', $analise), [
            'titulo' => 'Título Novo',
            'descricao' => null,
            'status' => Analise::STATUS_CONCLUIDA,
            'data_analise' => null,
        ]);

        $response->assertRedirect(route('analises.show', $analise));
        $this->assertSame('Título Novo', $analise->refresh()->titulo);
        $this->assertSame(Analise::STATUS_CONCLUIDA, $analise->status);
    }

    public function test_analise_pode_ser_removida_e_volta_para_o_cliente(): void
    {
        $user = User::factory()->create();
        $analise = Analise::factory()->create();

        $response = $this->actingAs($user)->delete(route('analises.destroy', $analise));

        $response->assertRedirect(route('clientes.show', $analise->cliente));
        $this->assertDatabaseMissing('analises', ['id' => $analise->id]);
    }

    public function test_analises_aparecem_no_detalhe_do_cliente(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();
        Analise::factory()->create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Análise Visível no Cliente',
        ]);

        $this->actingAs($user)
            ->get(route('clientes.show', $cliente))
            ->assertOk()
            ->assertSee('Análise Visível no Cliente');
    }

    public function test_qualquer_socio_pode_criar_analise(): void
    {
        $autor = User::factory()->create();
        $outroSocio = User::factory()->create();
        $cliente = Cliente::factory()->create(['user_id' => $autor->id]);

        $this->actingAs($outroSocio)
            ->post(route('clientes.analises.store', $cliente), [
                'titulo' => 'Análise criada por outro sócio',
                'status' => Analise::STATUS_RASCUNHO,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('analises', [
            'cliente_id' => $cliente->id,
            'titulo' => 'Análise criada por outro sócio',
        ]);
    }
}

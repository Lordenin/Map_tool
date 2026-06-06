<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_e_redirecionado_para_login(): void
    {
        $this->get('/clientes')->assertRedirect('/login');
    }

    public function test_listagem_e_exibida(): void
    {
        $user = User::factory()->create();
        Cliente::factory()->create(['nome' => 'Padaria do Zé']);

        $response = $this->actingAs($user)->get('/clientes');

        $response->assertOk()->assertSee('Padaria do Zé');
    }

    public function test_pagina_de_cadastro_e_exibida(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/clientes/create')->assertOk();
    }

    public function test_cliente_pode_ser_cadastrado_e_vinculado_ao_socio(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/clientes', [
            'nome' => 'Mercado Central',
            'segmento' => 'Varejo',
            'observacoes' => 'Cliente âncora do bairro.',
        ]);

        $cliente = Cliente::firstWhere('nome', 'Mercado Central');

        $this->assertNotNull($cliente);
        $this->assertSame($user->id, $cliente->user_id);
        $this->assertSame('Varejo', $cliente->segmento);
        $response->assertRedirect(route('clientes.show', $cliente));
    }

    public function test_nome_e_obrigatorio_no_cadastro(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/clientes/create')
            ->post('/clientes', ['nome' => '']);

        $response->assertSessionHasErrors('nome')->assertRedirect('/clientes/create');
        $this->assertDatabaseCount('clientes', 0);
    }

    public function test_detalhe_do_cliente_e_exibido(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['nome' => 'Farmácia Saúde']);

        $this->actingAs($user)
            ->get(route('clientes.show', $cliente))
            ->assertOk()
            ->assertSee('Farmácia Saúde');
    }

    public function test_pagina_de_edicao_e_exibida(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['nome' => 'Loja Antiga']);

        $this->actingAs($user)
            ->get(route('clientes.edit', $cliente))
            ->assertOk()
            ->assertSee('Loja Antiga');
    }

    public function test_cliente_pode_ser_atualizado(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['nome' => 'Nome Antigo']);

        $response = $this->actingAs($user)->put(route('clientes.update', $cliente), [
            'nome' => 'Nome Novo',
            'segmento' => 'Serviços',
            'observacoes' => null,
        ]);

        $response->assertRedirect(route('clientes.show', $cliente));
        $this->assertSame('Nome Novo', $cliente->refresh()->nome);
        $this->assertSame('Serviços', $cliente->segmento);
    }

    public function test_cliente_pode_ser_removido(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($user)->delete(route('clientes.destroy', $cliente));

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }

    public function test_carteira_e_compartilhada_entre_socios(): void
    {
        $autor = User::factory()->create();
        $outroSocio = User::factory()->create();
        $cliente = Cliente::factory()->create([
            'user_id' => $autor->id,
            'nome' => 'Cliente do Outro Sócio',
        ]);

        // Um sócio diferente do autor consegue ver e editar o cliente.
        $this->actingAs($outroSocio)
            ->get(route('clientes.show', $cliente))
            ->assertOk()
            ->assertSee('Cliente do Outro Sócio');

        $this->actingAs($outroSocio)
            ->put(route('clientes.update', $cliente), ['nome' => 'Editado por Outro'])
            ->assertRedirect(route('clientes.show', $cliente));

        $this->assertSame('Editado por Outro', $cliente->refresh()->nome);
    }
}

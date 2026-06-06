<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_a_raiz_redireciona_visitante_para_login(): void
    {
        // A ferramenta é interna: '/' não tem página pública, redireciona para o login.
        $this->get('/')->assertRedirect('/login');
    }
}

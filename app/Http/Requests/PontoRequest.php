<?php

namespace App\Http\Requests;

use App\Models\Ponto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PontoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Carteira compartilhada: qualquer sócio autenticado pode gerenciar pontos.
        // O acesso já é restrito pelo middleware 'auth' nas rotas.
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in([Ponto::TIPO_ESTABELECIMENTO, Ponto::TIPO_CONCORRENTE])],
            'nome' => ['required', 'string', 'max:255'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'endereco' => 'endereço',
            'observacoes' => 'observações',
        ];
    }
}

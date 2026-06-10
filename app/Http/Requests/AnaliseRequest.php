<?php

namespace App\Http\Requests;

use App\Models\Analise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnaliseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Carteira compartilhada: qualquer sócio autenticado pode criar/editar análises.
        // O acesso já é restrito pelo middleware 'auth' nas rotas.
        return true;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\In>>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys(Analise::STATUSES))],
            'data_analise' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'descricao' => 'descrição',
            'data_analise' => 'data da análise',
        ];
    }
}

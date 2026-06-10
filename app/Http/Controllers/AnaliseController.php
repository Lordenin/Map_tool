<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnaliseRequest;
use App\Models\Analise;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnaliseController extends Controller
{
    public function create(Cliente $cliente): View
    {
        return view('analises.create', [
            'cliente' => $cliente,
            'analise' => new Analise(),
        ]);
    }

    public function store(AnaliseRequest $request, Cliente $cliente): RedirectResponse
    {
        $analise = $cliente->analises()->create($request->validated());

        return redirect()
            ->route('analises.show', $analise)
            ->with('status', 'Análise criada com sucesso.');
    }

    public function show(Analise $analise): View
    {
        $analise->load(['cliente', 'estabelecimento', 'concorrentes']);

        return view('analises.show', compact('analise'));
    }

    public function edit(Analise $analise): View
    {
        $analise->load('cliente');

        return view('analises.edit', compact('analise'));
    }

    public function update(AnaliseRequest $request, Analise $analise): RedirectResponse
    {
        $analise->update($request->validated());

        return redirect()
            ->route('analises.show', $analise)
            ->with('status', 'Análise atualizada com sucesso.');
    }

    public function destroy(Analise $analise): RedirectResponse
    {
        $cliente = $analise->cliente;
        $analise->delete();

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('status', 'Análise removida com sucesso.');
    }
}

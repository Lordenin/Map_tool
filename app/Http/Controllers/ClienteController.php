<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::query()
            ->with('user')
            ->withCount('analises')
            ->latest()
            ->paginate(15);

        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.create', ['cliente' => new Cliente()]);
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        // Vincula ao sócio autenticado (auditoria de quem cadastrou).
        $cliente = $request->user()->clientes()->create($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('status', 'Cliente cadastrado com sucesso.');
    }

    public function show(Cliente $cliente): View
    {
        $cliente->load([
            'user',
            'analises' => fn ($query) => $query->withCount('pontos')->latest(),
        ]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('status', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('status', 'Cliente removido com sucesso.');
    }
}

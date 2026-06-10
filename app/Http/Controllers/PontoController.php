<?php

namespace App\Http\Controllers;

use App\Http\Requests\PontoRequest;
use App\Models\Analise;
use App\Models\Ponto;
use App\Services\CalculadoraDistancia;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PontoController extends Controller
{
    public function __construct(private readonly CalculadoraDistancia $calculadora)
    {
    }

    public function create(Analise $analise): View
    {
        $tipo = request()->query('tipo', Ponto::TIPO_CONCORRENTE);

        if (! in_array($tipo, [Ponto::TIPO_ESTABELECIMENTO, Ponto::TIPO_CONCORRENTE], true)) {
            $tipo = Ponto::TIPO_CONCORRENTE;
        }

        return view('pontos.create', [
            'analise' => $analise,
            'ponto' => new Ponto(['tipo' => $tipo]),
        ]);
    }

    public function store(PontoRequest $request, Analise $analise): RedirectResponse
    {
        $dados = $request->validated();

        // Regra: cada análise tem no máximo um estabelecimento (ponto de referência).
        if ($dados['tipo'] === Ponto::TIPO_ESTABELECIMENTO && $analise->estabelecimento()->exists()) {
            return back()
                ->withInput()
                ->withErrors(['tipo' => 'Esta análise já tem um estabelecimento. Edite o existente.']);
        }

        $analise->pontos()->create($dados);
        $this->calculadora->recalcularAnalise($analise);

        $mensagem = $dados['tipo'] === Ponto::TIPO_ESTABELECIMENTO
            ? 'Estabelecimento definido.'
            : 'Concorrente adicionado.';

        return redirect()
            ->route('analises.show', $analise)
            ->with('status', $mensagem);
    }

    public function edit(Ponto $ponto): View
    {
        $ponto->load('analise');

        return view('pontos.edit', [
            'analise' => $ponto->analise,
            'ponto' => $ponto,
        ]);
    }

    public function update(PontoRequest $request, Ponto $ponto): RedirectResponse
    {
        $dados = $request->validated();

        // O tipo de um ponto não muda na edição (evita virar/duplicar estabelecimento).
        unset($dados['tipo']);

        $ponto->update($dados);
        $this->calculadora->recalcularAnalise($ponto->analise);

        return redirect()
            ->route('analises.show', $ponto->analise)
            ->with('status', 'Ponto atualizado.');
    }

    public function destroy(Ponto $ponto): RedirectResponse
    {
        $analise = $ponto->analise;
        $eraEstabelecimento = $ponto->ehEstabelecimento();

        $ponto->delete();

        // Remover o estabelecimento invalida as distâncias dos concorrentes: zera todas.
        if ($eraEstabelecimento) {
            $this->calculadora->recalcularAnalise($analise);
        }

        return redirect()
            ->route('analises.show', $analise)
            ->with('status', 'Ponto removido.');
    }
}

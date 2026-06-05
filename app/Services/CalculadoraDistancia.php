<?php

namespace App\Services;

use App\Models\Analise;
use App\Models\Ponto;

/**
 * Calcula distâncias geográficas internamente, sem usar a API do Google.
 *
 * Usa a fórmula de Haversine, que calcula a distância do "grande círculo"
 * entre dois pontos sobre a esfera terrestre — precisão mais que suficiente
 * para análise de concorrência urbana (erro < 0,5% em distâncias curtas).
 */
class CalculadoraDistancia
{
    private const RAIO_TERRA_METROS = 6_371_000;

    /**
     * Distância em metros entre dois pares de coordenadas.
     */
    public function haversine(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        // min(1.0, ...) evita NaN por erro de ponto flutuante quando os pontos coincidem.
        $c = 2 * asin(min(1.0, sqrt($a)));

        return (int) round(self::RAIO_TERRA_METROS * $c);
    }

    /**
     * Recalcula e persiste a distância de cada concorrente até o estabelecimento
     * da análise. Chamado ao criar/editar pontos ou mover o estabelecimento.
     */
    public function recalcularAnalise(Analise $analise): void
    {
        $estabelecimento = $analise->estabelecimento()->first();

        if ($estabelecimento === null) {
            return;
        }

        $analise->concorrentes()->each(function (Ponto $concorrente) use ($estabelecimento) {
            $distancia = $this->haversine(
                $estabelecimento->latitude,
                $estabelecimento->longitude,
                $concorrente->latitude,
                $concorrente->longitude,
            );

            $concorrente->update(['distancia_metros' => $distancia]);
        });
    }
}

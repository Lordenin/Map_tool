<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ponto extends Model
{
    use HasFactory;

    public const TIPO_ESTABELECIMENTO = 'estabelecimento';
    public const TIPO_CONCORRENTE = 'concorrente';

    protected $table = 'pontos';

    protected $fillable = [
        'analise_id',
        'tipo',
        'nome',
        'endereco',
        'latitude',
        'longitude',
        'observacoes',
        'distancia_metros',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'distancia_metros' => 'integer',
        ];
    }

    public function analise(): BelongsTo
    {
        return $this->belongsTo(Analise::class);
    }

    public function anexos(): MorphMany
    {
        return $this->morphMany(Anexo::class, 'anexavel');
    }

    public function ehEstabelecimento(): bool
    {
        return $this->tipo === self::TIPO_ESTABELECIMENTO;
    }

    /**
     * Distância formatada para exibição (ex.: "850 m" ou "1,2 km").
     */
    public function distanciaFormatada(): ?string
    {
        if ($this->distancia_metros === null) {
            return null;
        }

        if ($this->distancia_metros < 1000) {
            return "{$this->distancia_metros} m";
        }

        return number_format($this->distancia_metros / 1000, 1, ',', '.') . ' km';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Analise extends Model
{
    use HasFactory;

    protected $table = 'analises';

    protected $fillable = [
        'cliente_id',
        'titulo',
        'descricao',
        'status',
        'data_analise',
    ];

    protected function casts(): array
    {
        return [
            'data_analise' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pontos(): HasMany
    {
        return $this->hasMany(Ponto::class);
    }

    /**
     * O estabelecimento do cliente nesta análise (ponto de referência das distâncias).
     */
    public function estabelecimento(): HasOne
    {
        return $this->hasOne(Ponto::class)->where('tipo', Ponto::TIPO_ESTABELECIMENTO);
    }

    public function concorrentes(): HasMany
    {
        return $this->hasMany(Ponto::class)->where('tipo', Ponto::TIPO_CONCORRENTE);
    }
}

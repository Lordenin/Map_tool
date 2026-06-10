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

    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_EM_ANDAMENTO = 'em_andamento';
    public const STATUS_CONCLUIDA = 'concluida';

    /**
     * Status válidos e seus rótulos legíveis (fonte única para validação e exibição).
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        self::STATUS_RASCUNHO => 'Rascunho',
        self::STATUS_EM_ANDAMENTO => 'Em andamento',
        self::STATUS_CONCLUIDA => 'Concluída',
    ];

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

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
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

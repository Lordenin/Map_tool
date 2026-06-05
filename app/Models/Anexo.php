<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Anexo extends Model
{
    use HasFactory;

    public const CATEGORIA_IMAGEM = 'imagem';
    public const CATEGORIA_DOCUMENTO = 'documento';

    protected $table = 'anexos';

    protected $fillable = [
        'nome_original',
        'caminho',
        'tipo_mime',
        'categoria',
        'tamanho',
    ];

    public function anexavel(): MorphTo
    {
        return $this->morphTo();
    }

    public function ehImagem(): bool
    {
        return $this->categoria === self::CATEGORIA_IMAGEM;
    }
}

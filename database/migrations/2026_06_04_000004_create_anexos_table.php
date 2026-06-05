<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anexos', function (Blueprint $table) {
            $table->id();
            // Polimórfico: permite anexar a um Ponto ou a uma Análise sem retrabalho futuro.
            $table->morphs('anexavel');
            $table->string('nome_original');
            $table->string('caminho');
            $table->string('tipo_mime')->nullable();
            // imagem | documento
            $table->string('categoria')->default('imagem');
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anexos');
    }
};

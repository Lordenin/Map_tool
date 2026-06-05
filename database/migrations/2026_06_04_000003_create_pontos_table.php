<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pontos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analise_id')->constrained('analises')->cascadeOnDelete();
            // estabelecimento | concorrente
            $table->string('tipo');
            $table->string('nome');
            $table->string('endereco')->nullable();
            // 7 casas decimais ~ 1cm de precisão; cabe lat (-90..90) e lng (-180..180).
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('observacoes')->nullable();
            // Distância em metros até o estabelecimento da análise (Haversine, pré-calculada).
            $table->unsignedInteger('distancia_metros')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pontos');
    }
};

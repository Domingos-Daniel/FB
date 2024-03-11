<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao');
            $table->string('area_foco');
            $table->string('publico_alvo');
            $table->text('objetivo');
            $table->text('metas');
            $table->unsignedBigInteger('id_orcamento');
            $table->foreign('id_orcamento')->references('id')->on('orcamentos');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->string('responsavel');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};

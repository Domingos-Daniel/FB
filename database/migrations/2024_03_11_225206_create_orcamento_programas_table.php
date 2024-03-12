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
        Schema::create('orcamento_programas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_programa');
            $table->unsignedBigInteger('id_orcamento');
            $table->foreign('id_programa')->references('id')->on('programas');
            $table->foreign('id_orcamento')->references('id')->on('orcamentos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamento_programas');
    }
};

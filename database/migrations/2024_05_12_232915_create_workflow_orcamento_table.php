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
        Schema::create('workflow_orcamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orcamento_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('prox_passo');
            $table->integer('num_aprovacoes_necessarias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_orcamento');
    }
};

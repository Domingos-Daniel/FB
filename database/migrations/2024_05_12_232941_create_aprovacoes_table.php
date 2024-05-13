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
        Schema::create('aprovacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_orcamento_id')->constrained()->onDelete('cascade');
            $table->foreignId('passo_workflow_id')->constrained()->onDelete('cascade');
            $table->string('status');
            // Adicione outros campos, se necessário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aprovacoes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etapa_origem_id')->constrained('workflow_stages')->onDelete('cascade');
            $table->foreignId('etapa_destino_id')->constrained('workflow_stages')->onDelete('cascade');
            $table->string('permissao_requerida')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_transitions');
    }
};

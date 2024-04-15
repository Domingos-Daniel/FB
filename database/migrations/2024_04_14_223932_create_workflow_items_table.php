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
        Schema::create('workflow_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('modelo');
            $table->foreignId('etapa_atual_id')->constrained('workflow_stages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_items');
    }
};

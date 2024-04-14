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
        Schema::create('subprograma_pessoas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_subprograma');
            $table->unsignedBigInteger('id_pessoa');
            $table->text('description')->nullable();
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->timestamps();
    
            $table->foreign('id_subprograma')->references('id')->on('subprogramas')->onDelete('cascade');
            $table->foreign('id_pessoa')->references('id')->on('pessoas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subprograma_pessoas');
    }
};

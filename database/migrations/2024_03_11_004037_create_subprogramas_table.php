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
        Schema::create('subprogramas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_programa');
            $table->string('designacao');
            $table->decimal('valor', 10, 2);
            $table->foreign('id_programa')->references('id')->on('programas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subprogramas');
    }
};

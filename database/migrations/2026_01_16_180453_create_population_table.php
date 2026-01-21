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
        Schema::create('population', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('municipio_id')->nullable();
            $table->foreign('municipio_id')->references('id')->on('municipio');

            $table->unsignedBigInteger('isla_id');
            $table->foreign('isla_id')->references('id')->on('isla');

            $table->year('year');
            $table->string('gender'); // Mujeres, Hombres, T
            $table->string('age');    // "De 5 a 9 años", "46 años", etc
            $table->integer('population')->nullable(); 
            $table->decimal('proportion', 5, 2)->nullable(); // porcentaje

            $table->timestamps();

            $table->index('municipio_id');
            $table->index('isla_id');
            $table->index(['year', 'gender']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population');
    }
};
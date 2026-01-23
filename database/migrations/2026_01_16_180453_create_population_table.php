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

            $table->string('gdc_municipio')->nullable();
            $table->foreign('gdc_municipio')->references('gdc_municipio')->on('municipio');

            $table->string('gdc_isla')->nullable();
            $table->foreign('gdc_isla')->references('gdc_isla')->on('isla');

            $table->year('year');
            $table->string('gender'); // Mujeres, Hombres, T
            $table->string('age');    // "De 5 a 9 años", "46 años", etc
            $table->integer('population')->nullable(); 
            $table->decimal('proportion', 5, 2)->nullable(); // porcentaje

            $table->timestamps();

            $table->index('gdc_municipio');
            $table->index('gdc_isla');
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
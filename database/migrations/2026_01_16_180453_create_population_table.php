<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('population', function (Blueprint $table) {
            $table->id();

            // 🔑 Claves GDC
            $table->string('gdc_municipio', 10)->nullable();
            $table->string('gdc_isla', 10)->nullable();

            // Datos demográficos
            $table->year('year');
            $table->string('gender');   // Mujeres, Hombres, Total
            $table->string('age');      // "De 5 a 9 años", "46 años"
            $table->integer('population')->nullable();
            $table->decimal('proportion', 6, 3)->nullable();

            $table->timestamps();

            // 🔗 Foreign keys (tipos STRING iguales)
            $table->foreign('gdc_municipio')
                ->references('gdc_municipio')
                ->on('municipio')
                ->nullOnDelete();

            $table->foreign('gdc_isla')
                ->references('gdc_isla')
                ->on('isla')
                ->nullOnDelete();

            // Índices útiles
            $table->index(['gdc_municipio', 'year']);
            $table->index(['gdc_isla', 'year']);
            $table->index(['year', 'gender']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('population');
    }
};
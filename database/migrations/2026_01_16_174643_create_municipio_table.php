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
        Schema::create('municipio', function (Blueprint $table) {
            $table->id();
            $table->string('isla_name');
            $table->string('name');
            $table->unsignedBigInteger('isla_id');
            $table->string('gdc_municipio')->unique();
            $table->string('gdc_isla');
            $table->foreign('isla_name')->references('name')->on('isla');
            $table->foreign('gdc_isla')->references('gdc_isla')->on('isla');
            $table->foreign('isla_id')->references('id')->on('isla');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipio');
    }
};

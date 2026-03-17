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
        Schema::create('espacios', function (Blueprint $table) {
            $table->integer('espacio_id')->primary();
            $table->string('esp_nombre', 30);
            $table->longText('esp_descripcion');
            $table->integer('esp_capacidad');
            $table->string('esp_tipo', 30);
            $table->decimal('esp_precio_hora', 10, 0);
            $table->string('esp_estado', 30);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('espacios');
    }
};

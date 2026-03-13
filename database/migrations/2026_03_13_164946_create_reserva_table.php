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
        Schema::create('reserva', function (Blueprint $table) {
            $table->integer('reserva_id', true);
            $table->time('rsva_hora_inicio');
            $table->time('rsva_hora_fin');
            $table->date('rsva_fecha');
            $table->string('rsva_estado', 30);
            $table->longText('rsva_descripcion');
            $table->integer('user_id')->index('user_id');
            $table->integer('espacio_id')->index('espacio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva');
    }
};

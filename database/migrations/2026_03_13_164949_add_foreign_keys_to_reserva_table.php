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
        Schema::table('reserva', function (Blueprint $table) {
            $table->foreign(['user_id'], 'reserva_ibfk_1')->references(['user_id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['espacio_id'], 'reserva_ibfk_2')->references(['espacio_id'])->on('espacios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserva', function (Blueprint $table) {
            $table->dropForeign('reserva_ibfk_1');
            $table->dropForeign('reserva_ibfk_2');
        });
    }
};

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
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->integer('reserva_id')->nullable()->after('espacio_id');
            // Nota: No añado foreign key estricta ahora para evitar conflictos si ya hay datos,
            // pero lo ideal sería vincularlo a 'reserva.reserva_id'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropColumn('reserva_id');
        });
    }
};

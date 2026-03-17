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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->string('user_id')->primary();
            $table->string('user_nombre');
            $table->string('user_correo')->unique();
            $table->bigInteger('user_telefono');
            $table->string('user_contrasena');
            $table->integer('rol_id')->index('rol_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};

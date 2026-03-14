<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;

Route::get('/cliente', [ClienteController::class, 'index'])->name('cliente.index');

Route::prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/buscar-espacios', [ClienteController::class, 'buscarEspacios'])->name('buscar_espacios');
    Route::get('/mis-reservas', [ClienteController::class, 'misReservas'])->name('mis_reservas');
    Route::get('/mi-perfil', [ClienteController::class, 'perfil'])->name('perfil');
});

// Ruta temporal para logout
Route::post('/logout', function () { return "Cerrando sesión..."; })->name('logout');

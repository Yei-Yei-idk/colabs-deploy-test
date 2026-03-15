<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InicioController;

Route::get('/', [InicioController::class, 'index'])->name('inicio');
Route::get('/nosotros', [InicioController::class, 'nosotros'])->name('nosotros');
Route::get('/ubicacion', [InicioController::class, 'ubicacion'])->name('ubicacion');
Route::get('/servicios', [InicioController::class, 'servicios'])->name('servicios');

Route::get('/cliente', [ClienteController::class, 'index'])->name('cliente.index');

Route::prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/buscar-espacios', [ClienteController::class, 'buscarEspacios'])->name('buscar_espacios');
    Route::get('/mis-reservas', [ClienteController::class, 'misReservas'])->name('mis_reservas');
    Route::get('/mi-perfil', [ClienteController::class, 'perfil'])->name('perfil');
});

// Ruta temporal para logout
Route::post('/logout', function () { return "Cerrando sesión..."; })->name('logout');

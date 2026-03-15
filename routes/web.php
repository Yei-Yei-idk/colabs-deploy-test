<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InicioController;

Route::get('/', [InicioController::class, 'index'])->name('inicio');
Route::get('/nosotros', [InicioController::class, 'nosotros'])->name('nosotros');
Route::get('/ubicacion', [InicioController::class, 'ubicacion'])->name('ubicacion');
Route::get('/servicios', [InicioController::class, 'servicios'])->name('servicios');

Route::prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/', [ClienteController::class, 'index'])->name('index');
    Route::get('/buscar', [ClienteController::class, 'buscarEspacios'])->name('buscar_espacios');
    Route::get('/reservas', [ClienteController::class, 'misReservas'])->name('mis_reservas');
    Route::get('/perfil', [ClienteController::class, 'perfil'])->name('perfil');
    Route::post('/perfil', [ClienteController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    Route::get('/reserva/{id}', [ClienteController::class, 'detallesReserva'])->name('detalles_reserva');
    Route::post('/reserva/cancelar', [ClienteController::class, 'cancelarReserva'])->name('cancelar_reserva');
    Route::post('/calificar', [ClienteController::class, 'calificarEspacio'])->name('calificar');
    Route::get('/reservar/{id}', [ClienteController::class, 'reservar'])->name('reservar');
    Route::post('/verificar-disponibilidad', [ClienteController::class, 'verificarDisponibilidad'])->name('verificar_disponibilidad');
    Route::post('/confirmar-reserva', [ClienteController::class, 'confirmarReserva'])->name('confirmar_reserva');
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
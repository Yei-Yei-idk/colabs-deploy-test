<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\Auth\RegistrarseController;
use App\Http\Controllers\Auth\IniciarSesionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EspaciosController;

Route::get('/', [InicioController::class, 'index'])->name('inicio');
Route::get('/nosotros', [InicioController::class, 'nosotros'])->name('nosotros');
Route::get('/ubicacion', [InicioController::class, 'ubicacion'])->name('ubicacion');
Route::get('/servicios', [InicioController::class, 'servicios'])->name('servicios');

// Registro de usuarios (equivalente a registrarse.php)
Route::get('/registrarse', [RegistrarseController::class, 'mostrar'])->name('registrarse.mostrar');
Route::post('/registrarse', [RegistrarseController::class, 'guardar'])->name('registrarse.guardar');

// Inicio de sesión (equivalente a login.php)
Route::get('/login', [IniciarSesionController::class, 'mostrarFormulario'])->name('login');
Route::post('/login', [IniciarSesionController::class, 'autenticar'])->name('login.autenticar');

Route::prefix('cliente')->name('cliente.')->middleware(['auth', 'es.cliente'])->group(function () {
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

// ===================== PANEL ADMIN / SUPER-ADMIN =====================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'es.administrador'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Espacios
        Route::get('/espacios', [EspaciosController::class, 'index'])->name('espacios.index');
        Route::get('/espacios/nuevo', [EspaciosController::class, 'create'])->name('espacios.create');
        Route::get('/espacios/{id}/editar', [EspaciosController::class, 'edit'])->name('espacios.edit');
        Route::put('/espacios/{id}', [EspaciosController::class, 'update'])->name('espacios.update');
        Route::post('/espacios/{id}/toggle', [EspaciosController::class, 'toggleStatus'])->name('espacios.toggle');

        // Los siguientes controladores se irán creando en las próximas migraciones:
        Route::get('/reservas',             fn() => 'próximamente')->name('reservas.index');
        Route::get('/reservas/pendientes',  fn() => 'próximamente')->name('reservas.pendientes');
        Route::get('/reservas/finalizadas', fn() => 'próximamente')->name('reservas.finalizadas');
        Route::get('/backup',               fn() => 'próximamente')->name('backup.index');
        Route::get('/usuarios',             fn() => 'próximamente')->name('usuarios.index');
    });
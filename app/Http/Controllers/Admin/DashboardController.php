<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use App\Models\Reserva;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard del administrador / superadmin.
     * Equivale a Dashboard_SU.php
     */
    public function index()
    {
        // COUNT(*) WHERE esp_estado = 'Activo'
        $espaciosDisponibles = Espacio::where('esp_estado', 'Activo')->count();

        // COUNT(*) WHERE rsva_estado = 'Aceptada'
        $reservas = Reserva::where('rsva_estado', 'Aceptada')->count();

        // COUNT(*) WHERE rsva_estado = 'Pendiente'
        $solicitudesPendientes = Reserva::where('rsva_estado', 'Pendiente')->count();

        // SELECT con JOIN a usuarios y espacios (últimas reservas)
        // Equivale al segundo bloque SQL con JOIN
        $ultimasReservas = Reserva::with(['usuario', 'espacio'])
            ->latest('rsva_fecha')
            ->get();

        return view('admin.dashboard', compact(
            'espaciosDisponibles',
            'reservas',
            'solicitudesPendientes',
            'ultimasReservas'
        ));
    }
}

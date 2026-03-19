<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservasController extends Controller
{
    /**
     * Calendario principal de reservas del día.
     * Equivalente a Reservas_SU.php
     */
    public function index(Request $request)
    {
        Carbon::setLocale('es');
        // Manejo de fecha con Carbon (reemplaza strtotime + date())
        $fechaInput = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $fecha      = Carbon::parse($fechaInput)->locale('es');

        $fechaAnterior  = $fecha->copy()->subDay()->format('Y-m-d');
        $fechaSiguiente = $fecha->copy()->addDay()->format('Y-m-d');

        // SELECT * FROM espacios
        $espacios = Espacio::all();

        // SELECT * FROM reserva WHERE rsva_fecha = '$fecha'
        // Solo estados activos para el calendario
        $reservas = Reserva::whereDate('rsva_fecha', $fecha->format('Y-m-d'))
            ->whereIn('rsva_estado', ['activa', 'aceptada', 'Activa', 'Aceptada'])
            ->get()
            ->groupBy('espacio_id'); // agrupa por espacio para búsqueda rápida en la vista

        return view('admin.reservas.index', compact(
            'espacios',
            'reservas',
            'fecha',
            'fechaAnterior',
            'fechaSiguiente'
        ));
    }

    /**
     * Lista de reservas pendientes de aprobación.
     * Equivalente a Pendientes.php
     */
    public function pendientes()
    {
        Carbon::setLocale('es');
        $reservas = Reserva::with(['espacio', 'usuario'])
            ->whereIn('rsva_estado', ['Pendiente', 'pendiente'])
            ->orderBy('rsva_fecha', 'asc')
            ->get();

        return view('admin.reservas.pendientes', compact('reservas'));
    }

    /**
     * Lista de reservas finalizadas.
     * Equivalente a Finalizadas.php
     */
    public function finalizadas(Request $request)
    {
        Carbon::setLocale('es');

        // Manejo de fecha con Carbon (reemplaza $_GET['fecha'])
        $fechaInput = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $fecha      = Carbon::parse($fechaInput)->locale('es');

        $fechaAnterior  = $fecha->copy()->subDay()->format('Y-m-d');
        $fechaSiguiente = $fecha->copy()->addDay()->format('Y-m-d');

        $reservas = Reserva::with(['espacio', 'usuario'])
            ->whereDate('rsva_fecha', $fecha->format('Y-m-d'))
            ->whereIn('rsva_estado', ['Finalizada', 'finalizada'])
            ->orderBy('rsva_fecha', 'desc')
            ->get();

        return view('admin.reservas.finalizadas', compact(
            'reservas',
            'fecha',
            'fechaAnterior',
            'fechaSiguiente'
        ));
    }

    /**
     * Actualiza el estado de una reserva (Aceptada/Rechazada).
     * Reemplaza a nuevo_estado.php
     */
    public function actualizarEstado(Request $request)
    {
        $request->validate([
            'reserva_id' => 'required|exists:reserva,reserva_id',
            'nuevo_estado' => 'required|string'
        ]);

        $reserva = Reserva::findOrFail($request->reserva_id);
        $reserva->rsva_estado = $request->nuevo_estado;
        $reserva->save();

        return back()->with('success', "La reserva #{$reserva->reserva_id} ha sido {$request->nuevo_estado}.");
    }
}

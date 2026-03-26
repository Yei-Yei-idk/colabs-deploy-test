<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MÉTODO PRIVADO — se llama al inicio de todos los métodos de reservas
    |--------------------------------------------------------------------------
    | Busca en la BD todas las reservas con estado activo cuya fecha/hora
    | de fin ya pasó y las marca como "finalizada".
    | Usa la hora real de Colombia gracias a 'timezone' => 'America/Bogota'
    | definido en config/app.php
    */
    private function actualizarVencidas(): void
    {
        $ahora = Carbon::now(); // America/Bogota

        Reserva::whereIn('rsva_estado', ['activa', 'Activa', 'aceptada', 'Aceptada'])
            ->where(function ($q) use ($ahora) {
                // Caso 1: la fecha de la reserva ya pasó (ayer o antes)
                $q->whereDate('rsva_fecha', '<', $ahora->toDateString())
                  // Caso 2: es hoy pero la hora de fin ya pasó
                  ->orWhere(function ($q2) use ($ahora) {
                      $q2->whereDate('rsva_fecha', $ahora->toDateString())
                         ->whereTime('rsva_hora_fin', '<=', $ahora->toTimeString());
                  });
            })
            ->update(['rsva_estado' => 'finalizada']);
    }

    /**
     * Calendario principal de reservas del día.
     */
    public function index(Request $request)
    {
        $this->actualizarVencidas(); // ← actualiza DB antes de leer

        Carbon::setLocale('es');
        $fechaInput = $request->input('fecha', Carbon::today()->format('Y-m-d'));
        $fecha      = Carbon::parse($fechaInput)->locale('es');

        $fechaAnterior  = $fecha->copy()->subDay()->format('Y-m-d');
        $fechaSiguiente = $fecha->copy()->addDay()->format('Y-m-d');

        $espacios = Espacio::all();

        $reservas = Reserva::whereDate('rsva_fecha', $fecha->format('Y-m-d'))
            ->with('usuario')
            ->whereIn('rsva_estado', ['activa', 'aceptada', 'Activa', 'Aceptada', 'pendiente', 'Pendiente'])
            ->get()
            ->groupBy('espacio_id');

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
     */
    public function pendientes()
    {
        $this->actualizarVencidas(); // ← actualiza DB antes de leer

        Carbon::setLocale('es');
        $reservas = Reserva::with(['espacio', 'usuario'])
            ->whereIn('rsva_estado', ['Pendiente', 'pendiente'])
            ->orderBy('rsva_fecha', 'asc')
            ->get();

        return view('admin.reservas.pendientes', compact('reservas'));
    }

    /**
     * Lista de reservas finalizadas.
     */
    public function finalizadas(Request $request)
    {
        $this->actualizarVencidas(); // ← actualiza DB antes de leer

        Carbon::setLocale('es');
        $fechaInput = $request->input('fecha', Carbon::today()->format('Y-m-d'));
        $fecha      = Carbon::parse($fechaInput)->locale('es');

        $fechaAnterior  = $fecha->copy()->subDay()->format('Y-m-d');
        $fechaSiguiente = $fecha->copy()->addDay()->format('Y-m-d');

        $reservas = Reserva::with(['espacio', 'usuario'])
            ->whereDate('rsva_fecha', $fecha->format('Y-m-d'))
            ->whereIn('rsva_estado', ['Finalizada', 'finalizada'])
            ->orderBy('rsva_hora_fin', 'desc')
            ->get();

        return view('admin.reservas.finalizadas', compact(
            'reservas',
            'fecha',
            'fechaAnterior',
            'fechaSiguiente'
        ));
    }

    /**
     * Cambia el estado manual de una reserva (Aceptada / Rechazada).
     * Reemplaza a nuevo_estado.php
     */
    public function actualizarEstado(Request $request)
    {
        $request->validate([
            'reserva_id'   => 'required|exists:reserva,reserva_id',
            'nuevo_estado' => 'required|string',
        ]);

        $reserva = Reserva::findOrFail($request->reserva_id);
        $reserva->rsva_estado = $request->nuevo_estado;
        $reserva->save();

        return back()->with('success', "La reserva #{$reserva->reserva_id} ha sido {$request->nuevo_estado}.");
    }

    /**
     * JSON con reservas del día actual.
     */
    public function reservasDelDia()
    {
        $this->actualizarVencidas();

        $reservas = Reserva::with(['espacio', 'usuario'])
            ->whereDate('rsva_fecha', Carbon::today())
            ->get();

        return response()->json($reservas);
    }

    /**
     * Endpoint POST llamado por el JS del layout cada 60 s como respaldo.
     * POST /admin/reservas/sincronizar-estados
     */
    public function sincronizarEstados()
    {
        $ahora = Carbon::now();

        $actualizadas = Reserva::whereIn('rsva_estado', ['activa', 'Activa', 'aceptada', 'Aceptada', 'pendiente', 'Pendiente'])
            ->where(function ($q) use ($ahora) {
                $q->whereDate('rsva_fecha', '<', $ahora->toDateString())
                  ->orWhere(function ($q2) use ($ahora) {
                      $q2->whereDate('rsva_fecha', $ahora->toDateString())
                         ->whereTime('rsva_hora_fin', '<=', $ahora->toTimeString());
                  });
            })
            ->update(['rsva_estado' => 'finalizada']);

        return response()->json([
            'ok'           => true,
            'actualizadas' => $actualizadas,
            'hora'         => $ahora->format('H:i:s'),
        ]);
    }
}

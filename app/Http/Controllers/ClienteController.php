<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Espacio;
use App\Models\Reserva;
use App\Models\Calificacion;
use App\Models\Imagen;

class ClienteController extends Controller
{
    public function index()
    {
        // Eloquent: Obtener 5 espacios activos ordenados por nombre (espacios destacados)
        $espacios = Espacio::where('esp_estado', 'Activo')
                        ->orderBy('esp_nombre')
                        ->limit(5)
                        ->get();

        return view('cliente.index', compact('espacios'));
    }

    public function buscarEspacios(Request $request)
    {
        $tipo = $request->input('esp_tipo');
        $capacidad = $request->input('esp_capacidad');
        $precioMax = $request->input('esp_precio_hora');

        // Eloquent base query con eager loading de la imagen
        $query = Espacio::with('imagen')->where('esp_estado', 'Activo');

        if (!empty($tipo)) {
            $query->where('esp_tipo', $tipo);
        }

        if (!empty($capacidad)) {
            $query->where('esp_capacidad', '>=', (int)$capacidad);
        }

        if (!empty($precioMax)) {
            $query->where('esp_precio_hora', '<=', (int)$precioMax);
        }

        $espacios = $query->get();

        return view('cliente.buscar_espacios', compact('espacios', 'tipo', 'capacidad', 'precioMax'));
    }

    public function misReservas()
    {
        $user_id = Auth::id(); // Usar el ID del usuario autenticado

        $reservas = Reserva::where('user_id', $user_id)
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->select(
                'reserva.reserva_id',
                'reserva.espacio_id',
                'espacios.esp_nombre',
                'espacios.esp_descripcion',
                'espacios.esp_precio_hora',
                'reserva.rsva_fecha as fecha',
                'reserva.rsva_hora_inicio as hora_inicio',
                'reserva.rsva_hora_fin as hora_fin',
                'reserva.rsva_estado as estado'
            )
            ->orderBy('reserva.rsva_fecha', 'DESC')
            ->get();

        return view('cliente.mis_reservas', compact('reservas'));
    }

    public function perfil()
    {
        $usuario = Auth::user();
        return view('cliente.perfil', compact('usuario'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user_id = Auth::id();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:usuarios,user_correo,' . $user_id . ',user_id',
            'telefono' => 'required|string|max:15',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El formato del correo es inválido.',
            'email.unique' => 'El correo electrónico ya está en uso por otro usuario.',
            'telefono.required' => 'El teléfono es obligatorio.'
        ]);

        $updateData = [
            'user_nombre' => $request->nombre,
            'user_correo' => $request->email,
            'user_telefono' => $request->telefono,
        ];

        if ($request->filled('password')) {
            // Aquí iría la validación de la auth y bcrypt para la nueva contraseña.
            // Para el alcance temporal, simplemente la actualizamos:
            $updateData['user_contrasena'] = bcrypt($request->newpassword);
        }

        DB::table('usuarios')->where('user_id', $user_id)->update($updateData);

        return redirect()->route('cliente.perfil')->with('success', '✅ Perfil actualizado correctamente');
    }

    public function detallesReserva($id)
    {
        $user_id = Auth::id();

        $reserva = Reserva::where('reserva_id', $id)
            ->where('user_id', $user_id)
            ->join('espacios', 'reserva.espacio_id', '=', 'espacios.espacio_id')
            ->select(
                'reserva.reserva_id',
                'reserva.espacio_id',
                'reserva.rsva_fecha as fecha',
                'reserva.rsva_hora_inicio as hora_inicio',
                'reserva.rsva_hora_fin as hora_fin',
                'reserva.rsva_estado as estado',
                'espacios.esp_nombre',
                'espacios.esp_descripcion',
                'espacios.esp_precio_hora',
                'espacios.esp_capacidad',
                'espacios.esp_tipo'
            )->firstOrFail();

        $tiene_calificacion = Calificacion::where('espacio_id', $reserva->espacio_id)
            ->where('user_id', $user_id)
            ->exists();

        // Obtener solo una imagen para la vista
        $imagen = Imagen::where('espacio_id', $reserva->espacio_id)->first();
        $imgSrc = $imagen ? $imagen->foto : null;

        return view('cliente.detalles_reserva', compact('reserva', 'tiene_calificacion', 'imgSrc'));
    }

    public function cancelarReserva(Request $request)
    {
        $user_id = Auth::id();
        $reserva_id = $request->reserva_id;

        $reserva = Reserva::where('reserva_id', $reserva_id)
            ->where('user_id', $user_id)
            ->firstOrFail();

        if ($reserva->rsva_estado === 'Pendiente') {
            $reserva->update(['rsva_estado' => 'Cancelada']);
            return redirect()->route('cliente.mis_reservas')->with('success', '✅ Tu reserva ha sido cancelada correctamente.');
        } elseif ($reserva->rsva_estado === 'Aceptada') {
            return back()->with('error', '⚠️ No puedes cancelar una reserva que ya ha sido aceptada. Contacta al administrador.');
        } else {
            return back()->with('error', '⚠️ Esta reserva ya no puede cancelarse.');
        }
    }

    public function calificarEspacio(Request $request)
    {
        $user_id = Auth::id();

        $request->validate([
            'espacio_id' => 'required|integer',
            'reserva_id' => 'required|integer',
            'calif_puntuacion' => 'required|integer|min:1|max:5',
            'calif_txt' => 'required|string|max:1800',
        ]);

        // Evitar duplicados
        $existe = Calificacion::where('reserva_id', $request->reserva_id)->exists();
        if ($existe) {
            return back()->with('error', '⚠️ Ya has calificado esta reserva anteriormente.');
        }

        Calificacion::create([
            'calif_txt' => $request->calif_txt,
            'calif_puntuacion' => $request->calif_puntuacion,
            'user_id' => $user_id,
            'espacio_id' => $request->espacio_id,
            'reserva_id' => $request->reserva_id
        ]);

        return back()->with('success', '¡Gracias por tu calificación! Tu opinión ha sido registrada exitosamente.');
    }

    /**
     * Muestra la página de reserva de un espacio específico
     */
    public function reservar($id)
    {
        $espacio = Espacio::findOrFail($id);

        // Obtener primera imagen del espacio
        $imagenes = Imagen::where('espacio_id', $id)->limit(1)->pluck('foto')->toArray();

        // Obtener calificaciones con nombre del usuario
        $calificaciones = Calificacion::where('espacio_id', $id)
            ->join('usuarios', 'calificaciones.user_id', '=', 'usuarios.user_id')
            ->select('calificaciones.calif_txt', 'calificaciones.calif_id', 'calificaciones.calif_puntuacion', 'usuarios.user_nombre', 'usuarios.user_id')
            ->orderBy('calificaciones.calif_id', 'DESC')
            ->get()
            ->toArray();

        $num_resenas = count($calificaciones);
        $total_puntos = array_sum(array_column($calificaciones, 'calif_puntuacion'));
        $promedio = $num_resenas > 0 ? number_format(round($total_puntos / $num_resenas, 1), 1) : '0.0';

        return view('cliente.reservar', compact('espacio', 'imagenes', 'calificaciones', 'num_resenas', 'promedio'));
    }

    /**
     * Verificar disponibilidad del espacio (AJAX)
     */
    public function verificarDisponibilidad(Request $request)
    {
        $espacio_id = (int) $request->input('espacio_id');
        $fecha = $request->input('fecha');
        $hora_inicio = $request->input('hora_inicio');
        $hora_fin = $request->input('hora_fin');

        // Validar hora_fin > hora_inicio
        if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'La hora de fin debe ser posterior a la hora de inicio'
            ]);
        }

        // Validar que no sea en el pasado
        $fechaHoraInicio = \Carbon\Carbon::parse($fecha . ' ' . $hora_inicio);
        if ($fechaHoraInicio->isPast()) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'No puedes reservar en una fecha u hora que ya pasó.'
            ]);
        }

        // Verificar que el espacio exista y esté activo
        $espacio = Espacio::find($espacio_id);
        if (!$espacio || $espacio->esp_estado !== 'Activo') {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'Este espacio no está disponible'
            ]);
        }

        // Verificar conflictos con Eloquent
        $conflicto = Reserva::where('espacio_id', $espacio_id)
            ->where('rsva_fecha', $fecha)
            ->whereIn('rsva_estado', ['Aceptada', 'Pendiente'])
            ->where(function ($q) use ($hora_inicio, $hora_fin) {
                $q->where(function ($sub) use ($hora_inicio, $hora_fin) {
                    // Reserva existente cubre parte del rango solicitado
                    $sub->where('rsva_hora_inicio', '<', $hora_fin)
                        ->where('rsva_hora_fin', '>', $hora_inicio);
                })->orWhere(function ($sub) use ($hora_inicio, $hora_fin) {
                    // Reserva existente está dentro del rango solicitado
                    $sub->where('rsva_hora_inicio', '>=', $hora_inicio)
                        ->where('rsva_hora_fin', '<=', $hora_fin);
                });
            })
            ->first();

        if ($conflicto) {
            $esPropia = $conflicto->user_id === Auth::id();

            if ($conflicto->rsva_estado === 'Aceptada') {
                return response()->json([
                    'disponible' => false,
                    'estado' => $esPropia ? 'TuyaAceptada' : 'Aceptada',
                    'mensaje' => $esPropia ? 'Ya tienes esta reserva aceptada.' : 'El horario seleccionado ya está reservado.'
                ]);
            } elseif ($conflicto->rsva_estado === 'Pendiente') {
                return response()->json([
                    'disponible' => !$esPropia, // Permitimos competir a menos que ya sea tuya
                    'estado' => $esPropia ? 'TuyaPendiente' : 'Pendiente',
                    'mensaje' => $esPropia ? 'Ya tienes una solicitud en espera aquí.' : 'Hay solicitudes en espera, pero puedes reservar.'
                ]);
            }
        }

        // Generar bloques de tiempo por hora
        $bloques = [];
        $inicio = new \DateTime($fecha . ' ' . $hora_inicio);
        $fin = new \DateTime($fecha . ' ' . $hora_fin);
        $current = clone $inicio;

        while ($current < $fin) {
            $next = clone $current;
            $next->modify('+1 hour');
            if ($next > $fin) {
                $next = clone $fin;
            }
            $bloques[] = [
                'inicio' => $current->format('H:i'),
                'fin' => $next->format('H:i'),
            ];
            $current = $next;
        }

        return response()->json([
            'disponible' => true,
            'estado' => 'Libre',
            'mensaje' => 'Espacio disponible',
            'bloques' => $bloques,
        ]);
    }

    /**
     * Busca espacios alternativos disponibles cuando el horario solicitado está ocupado
     */
    public function alternativas(Request $request)
    {
        $espacio_id = $request->input('espacio_id');
        $fecha = $request->input('fecha');
        $hora_inicio = $request->input('hora_inicio');
        $hora_fin = $request->input('hora_fin');

        $espacioOriginal = Espacio::findOrFail($espacio_id);

        // Rango de precio +/- 30%
        $margen_precio = $espacioOriginal->esp_precio_hora * 0.3;
        $precio_min = $espacioOriginal->esp_precio_hora - $margen_precio;
        $precio_max = $espacioOriginal->esp_precio_hora + $margen_precio;

        $espaciosPotenciales = Espacio::with('imagen')
            ->where('esp_estado', 'Activo')
            ->where('esp_tipo', $espacioOriginal->esp_tipo)
            ->where('espacio_id', '!=', $espacio_id)
            ->whereBetween('esp_precio_hora', [$precio_min, $precio_max])
            ->get();

        $alternativas = [];

        foreach ($espaciosPotenciales as $espacio) {
            // Verificar solapamiento en Aceptada
            $conflicto = Reserva::where('espacio_id', $espacio->espacio_id)
                ->where('rsva_fecha', $fecha)
                ->where('rsva_estado', 'Aceptada')
                ->where(function ($q) use ($hora_inicio, $hora_fin) {
                    $q->where(function ($sub) use ($hora_inicio, $hora_fin) {
                        $sub->where('rsva_hora_inicio', '<', $hora_fin)
                            ->where('rsva_hora_fin', '>', $hora_inicio);
                    })->orWhere(function ($sub) use ($hora_inicio, $hora_fin) {
                        $sub->where('rsva_hora_inicio', '>=', $hora_inicio)
                            ->where('rsva_hora_fin', '<=', $hora_fin);
                    });
                })->exists();

            if (!$conflicto) {
                $alternativas[] = [
                    'id' => $espacio->espacio_id,
                    'nombre' => $espacio->esp_nombre,
                    'precio' => $espacio->esp_precio_hora,
                    'capacidad' => $espacio->esp_capacidad,
                    'imagen' => $espacio->imagen ? asset('uploads/' . $espacio->imagen->foto) : asset('uploads/OF1 .jpeg')
                ];

                if (count($alternativas) >= 3) break; // Traer máximo 3 sugerencias top
            }
        }

        return response()->json([
            'success' => count($alternativas) > 0,
            'alternativas' => $alternativas
        ]);
    }

    /**
     * Confirmar y guardar la reserva
     */
    public function confirmarReserva(Request $request)
    {
        // Validar que no sea en el pasado
        $fechaHoraInicio = \Carbon\Carbon::parse($request->fecha . ' ' . $request->hora_inicio);
        if ($fechaHoraInicio->isPast()) {
            return back()->with('error', '⚠️ No puedes realizar reservas en el pasado.');
        }

        // Validar capacidad en el servidor por seguridad
        $espacio = Espacio::findOrFail($request->espacio_id);
        if ($request->num_invitados > $espacio->esp_capacidad) {
            return back()->with('error', '⚠️ El número de invitados excede la capacidad máxima del espacio.');
        }

        // Double check de conflictos antes de crear (Carrera Crítica)
        $conflicto = Reserva::where('espacio_id', $request->espacio_id)
            ->where('rsva_fecha', $request->fecha)
            ->whereIn('rsva_estado', ['Aceptada', 'Pendiente'])
            ->where(function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('rsva_hora_inicio', '<', $request->hora_fin)
                        ->where('rsva_hora_fin', '>', $request->hora_inicio);
                })->orWhere(function ($sub) use ($request) {
                    $sub->where('rsva_hora_inicio', '>=', $request->hora_inicio)
                        ->where('rsva_hora_fin', '<=', $request->hora_fin);
                });
            })
            ->first();

        if ($conflicto) {
            if ($conflicto->rsva_estado === 'Aceptada') {
                return back()->with('error', '⚠️ Lo sentimos, alguien más acaba de reservar este espacio en ese horario.');
            } elseif ($conflicto->user_id === Auth::id() && $conflicto->rsva_estado === 'Pendiente') {
                return back()->with('error', '⚠️ Ya tienes una solicitud en espera para este horario.');
            }
        }

        // Crear la reserva con Eloquent
        Reserva::create([
            'user_id' => Auth::id(),
            'espacio_id' => $request->espacio_id,
            'rsva_fecha' => $request->fecha,
            'rsva_hora_inicio' => $request->hora_inicio,
            'rsva_hora_fin' => $request->hora_fin,
            'rsva_estado' => 'Pendiente',
            'rsva_descripcion' => $request->descripcion,
            'rsva_num_invitados' => $request->num_invitados,
        ]);

        return redirect()->route('cliente.mis_reservas')->with('success', '¡Reserva creada exitosamente! Está pendiente de aprobación.');
    }
}

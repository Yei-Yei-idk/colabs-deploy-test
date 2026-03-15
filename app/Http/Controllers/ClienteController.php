<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        // Eloquent: Obtener 5 espacios activos ordenados por nombre
        $espacios = \App\Models\Espacio::where('esp_estado', 'Activo')
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
        $query = \App\Models\Espacio::with('imagen')->where('esp_estado', 'Activo');

        if (!empty($tipo)) {
            $query->where('esp_tipo', $tipo);
        }

        if (!empty($capacidad)) {
            // Generalmente, cuando alguien busca capacidad para "10 personas",
            // necesita espacios que tengan capacidad de 10 o MÁS personas.
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
        $user_id = auth()->id(); // Usar el ID del usuario autenticado

        $reservas = \App\Models\Reserva::where('user_id', $user_id)
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
        $usuario = auth()->user();
        return view('cliente.perfil', compact('usuario'));
    }

    public function actualizarPerfil(\Illuminate\Http\Request $request)
    {
        $user_id = auth()->id();

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

        \DB::table('usuarios')->where('user_id', $user_id)->update($updateData);

        return redirect()->route('cliente.perfil')->with('success', '✅ Perfil actualizado correctamente');
    }

    public function detallesReserva($id)
    {
        $user_id = auth()->id();

        $reserva = \App\Models\Reserva::where('reserva_id', $id)
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

        $tiene_calificacion = \App\Models\Calificacion::where('espacio_id', $reserva->espacio_id)
            ->where('user_id', $user_id)
            ->exists();

        // Obtener solo una imagen para la vista
        $imagen = \App\Models\Imagen::where('espacio_id', $reserva->espacio_id)->first();
        $imgSrc = $imagen ? $imagen->foto : null;

        return view('cliente.detalles_reserva', compact('reserva', 'tiene_calificacion', 'imgSrc'));
    }

    public function cancelarReserva(\Illuminate\Http\Request $request)
    {
        $user_id = auth()->id();
        $reserva_id = $request->reserva_id;

        $reserva = \App\Models\Reserva::where('reserva_id', $reserva_id)
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

    public function calificarEspacio(\Illuminate\Http\Request $request)
    {
        $user_id = auth()->id();

        $request->validate([
            'espacio_id' => 'required|integer',
            'reserva_id' => 'required|integer',
            'calif_puntuacion' => 'required|integer|min:1|max:5',
            'calif_txt' => 'required|string|max:1800',
        ]);

        // Evitar duplicados
        $existe = \App\Models\Calificacion::where('reserva_id', $request->reserva_id)->exists();
        if ($existe) {
            return back()->with('error', '⚠️ Ya has calificado esta reserva anteriormente.');
        }

        \App\Models\Calificacion::create([
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
        $espacio = \App\Models\Espacio::findOrFail($id);

        // Obtener primera imagen del espacio
        $imagenes = \App\Models\Imagen::where('espacio_id', $id)->limit(1)->pluck('foto')->toArray();

        // Obtener calificaciones con nombre del usuario
        $calificaciones = \App\Models\Calificacion::where('espacio_id', $id)
            ->join('usuarios', 'calificaciones.user_id', '=', 'usuarios.user_id')
            ->select('calificaciones.calif_txt', 'calificaciones.calif_id', 'calificaciones.calif_puntuacion', 'usuarios.user_nombre')
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

        // Verificar que el espacio exista y esté activo
        $espacio = \App\Models\Espacio::find($espacio_id);
        if (!$espacio || $espacio->esp_estado !== 'Activo') {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'Este espacio no está disponible'
            ]);
        }

        // Verificar conflictos con Eloquent
        $conflictos = \App\Models\Reserva::where('espacio_id', $espacio_id)
            ->where('rsva_fecha', $fecha)
            ->whereIn('rsva_estado', ['Pendiente', 'Confirmada'])
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
            ->count();

        if ($conflictos > 0) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'El horario seleccionado ya está reservado. Por favor, elige otro horario.'
            ]);
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
            'mensaje' => 'Espacio disponible',
            'bloques' => $bloques,
        ]);
    }

    /**
     * Confirmar y guardar la reserva
     */
    public function confirmarReserva(Request $request)
    {
        // Validar capacidad en el servidor por seguridad
        $espacio = \App\Models\Espacio::findOrFail($request->espacio_id);
        if ($request->num_invitados > $espacio->esp_capacidad) {
            return back()->with('error', '⚠️ El número de invitados excede la capacidad máxima del espacio.');
        }

        // Crear la reserva con Eloquent
        \App\Models\Reserva::create([
            'user_id' => auth()->id(),
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

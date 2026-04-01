<?php

namespace App\Observers;

use App\Models\Reserva;
use App\Notifications\ReservaStatusChanged;

class ReservaObserver
{
    /**
     * Se dispara al CREAR una reserva.
     */
    public function created(Reserva $reserva): void
    {
        if ($reserva->rsva_estado == 'Pendiente') {
            $reserva->usuario->notify(new ReservaStatusChanged($reserva, 'Pendiente'));
        }
    }

    /**
     * Se dispara al ACTUALIZAR una reserva.
     */
    public function updated(Reserva $reserva): void
    {
        // Solo si el estado cambia (isDirty)
        if ($reserva->isDirty('rsva_estado')) {
            $nuevoEstado = $reserva->rsva_estado;

            // Mapeo seguro de estados para disparar notificaciones
            $estadosNotificables = ['Aceptada', 'Rechazada', 'Cancelada', 'Finalizada'];

            // Normalización simple (el campo puede venir en minúsculas por procesos automáticos)
            $estadoNormalizado = ucfirst(strtolower($nuevoEstado));

            if (in_array($estadoNormalizado, $estadosNotificables)) {
                $reserva->usuario->notify(new ReservaStatusChanged($reserva, $estadoNormalizado));
            }
        }
    }
}

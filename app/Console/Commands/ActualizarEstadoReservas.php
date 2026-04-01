<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActualizarEstadoReservas extends Command
{
    /**
     * Nombre del comando Artisan.
     * Ejecutar manualmente: php artisan reservas:actualizar-estado
     */
    protected $signature = 'reservas:actualizar-estado';

    protected $description = 'Marca como "finalizada" toda reserva activa cuya fecha/hora ya haya pasado';

    public function handle(): void
    {
        $ahora = Carbon::now();

        // Estados que deben pasar a "finalizada" cuando el tiempo expire
        $estadosAfinalizar = ['activa', 'Activa', 'aceptada', 'Aceptada', 'pendiente', 'Pendiente'];

        $reservas = Reserva::whereIn('rsva_estado', $estadosAfinalizar)
            ->where(function ($query) use ($ahora) {
                $query->whereDate('rsva_fecha', '<', $ahora->toDateString())
                    ->orWhere(function ($q) use ($ahora) {
                        $q->whereDate('rsva_fecha', $ahora->toDateString())
                          ->whereTime('rsva_hora_fin', '<=', $ahora->toTimeString());
                    });
            })
            ->get();

        /** @var Reserva $reserva */
        foreach ($reservas as $reserva) {
            $reserva->rsva_estado = 'Finalizada';
            $reserva->save(); // Al usar save(), el ReservaObserver detecta el cambio y envía el correo
        }

        $actualizadas = $reservas->count();

        $this->info("[{$ahora->format('Y-m-d H:i:s')}] {$actualizadas} reserva(s) marcadas como finalizadas.");
    }
}

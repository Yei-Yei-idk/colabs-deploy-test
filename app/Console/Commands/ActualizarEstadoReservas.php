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

        $actualizadas = Reserva::where('rsva_estado', 'activa')
            ->where(function ($query) use ($ahora) {
                // Caso 1: la fecha de la reserva ya pasó
                $query->whereDate('rsva_fecha', '<', $ahora->toDateString())
                    // Caso 2: es hoy pero la hora de fin ya pasó
                    ->orWhere(function ($q) use ($ahora) {
                        $q->whereDate('rsva_fecha', $ahora->toDateString())
                          ->whereTime('rsva_hora_fin', '<=', $ahora->toTimeString());
                    });
            })
            ->update(['rsva_estado' => 'finalizada']);

        $this->info("[{$ahora->format('Y-m-d H:i:s')}] {$actualizadas} reserva(s) marcadas como finalizadas.");
    }
}

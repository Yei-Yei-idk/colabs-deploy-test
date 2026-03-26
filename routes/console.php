<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Schedule: Actualización automática de reservas finalizadas
|--------------------------------------------------------------------------
| Ejecuta el command cada minuto para detectar reservas cuya fecha/hora
| de fin ya pasó y marcarlas como "finalizada" automáticamente.
|
| Para activarlo en producción, agregar al cron del servidor:
|   * * * * * php /ruta/del/proyecto/artisan schedule:run >> /dev/null 2>&1
*/
Schedule::command('reservas:actualizar-estado')->everyMinute();

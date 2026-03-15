<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reserva';
    protected $primaryKey = 'reserva_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'espacio_id',
        'rsva_fecha',
        'rsva_hora_inicio',
        'rsva_hora_fin',
        'rsva_estado',
        'rsva_descripcion',
        'rsva_num_invitados',
    ];

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id', 'espacio_id');
    }
}

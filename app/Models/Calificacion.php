<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';
    protected $primaryKey = 'calif_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'espacio_id',
        'reserva_id',
        'calif_puntuacion',
        'calif_txt',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id', 'espacio_id');
    }
}

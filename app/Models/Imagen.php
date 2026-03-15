<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';
    public $timestamps = false; // Sin created_at

    protected $fillable = [
        'espacio_id',
        'foto'
    ];

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id', 'espacio_id');
    }
}

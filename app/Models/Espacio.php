<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    protected $table = 'espacios';
    protected $primaryKey = 'espacio_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'espacio_id',
        'esp_nombre',
        'esp_descripcion',
        'esp_capacidad',
        'esp_tipo',
        'esp_precio_hora',
        'esp_estado'
    ];

    public function imagen()
    {
        return $this->hasOne(Imagen::class, 'espacio_id', 'espacio_id');
    }
}

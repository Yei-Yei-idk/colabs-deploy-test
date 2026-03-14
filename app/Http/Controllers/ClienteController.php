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
            $query->where('esp_capacidad', '<=', (int)$capacidad);
        }

        if (!empty($precioMax)) {
            $query->where('esp_precio_hora', '<=', (int)$precioMax);
        }

        $espacios = $query->get();

        return view('cliente.buscar_espacios', compact('espacios', 'tipo', 'capacidad', 'precioMax'));
    }

    public function misReservas()
    {
        return "Mis Reservas...";
    }

    public function perfil()
    {
        return "Mi Perfil...";
    }
}

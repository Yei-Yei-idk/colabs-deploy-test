<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use Illuminate\Http\Request;

class EspaciosController extends Controller
{
    /**
     * Lista todos los espacios con sus estadísticas.
     */
    public function index()
    {
        // Estadísticas
        $total_espacios = Espacio::count();
        $total_espacios_activos = Espacio::where('esp_estado', 'Activo')->count();
        $total_espacios_inactivos = Espacio::where('esp_estado', 'Inactivo')->count();

        // Listado de espacios
        $espacios = Espacio::all();

        return view('admin.espacios.index', compact(
            'total_espacios',
            'total_espacios_activos',
            'total_espacios_inactivos',
            'espacios'
        ));
    }

    /**
     * Alterna el estado de un espacio entre Activo e Inactivo.
     */
    public function toggleStatus($id)
    {
        $espacio = Espacio::findOrFail($id);

        // Lógica IF(esp_estado = 'Activo', 'Inactivo', 'Activo')
        $espacio->esp_estado = ($espacio->esp_estado === 'Activo') ? 'Inactivo' : 'Activo';
        $espacio->save();

        return back()->with('status', 'Estado del espacio "' . $espacio->esp_nombre . '" actualizado a ' . $espacio->esp_estado);
    }

    /**
     * Muestra el formulario para crear un espacio.
     */
    public function create()
    {
        return "Formulario de nuevo espacio - Próximamente (Pásame el archivo Nuevo_Espacio.php)";
    }

    /**
     * Muestra el formulario para editar un espacio.
     */
    public function edit($id)
    {
        $espacio = Espacio::findOrFail($id);
        return view('admin.espacios.edit', compact('espacio'));
    }

    /**
     * Procesa la actualización de un espacio.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'capacidad'    => 'required|integer|min:1',
            'tipo_oficina' => 'required|string',
            'Precio_hora'  => 'required|numeric',
        ]);

        $espacio = Espacio::findOrFail($id);
        
        $espacio->update([
            'esp_nombre'      => $request->nombre,
            'esp_descripcion' => $request->descripcion,
            'esp_capacidad'   => $request->capacidad,
            'esp_tipo'        => $request->tipo_oficina,
            'esp_precio_hora' => $request->Precio_hora,
        ]);

        return redirect()->route('admin.espacios.index')
            ->with('status', '✔️ Espacio actualizado correctamente');
    }
}

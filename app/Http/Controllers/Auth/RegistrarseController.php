<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrarseController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function mostrar()
    {
        return view('auth.registrarse');
    }

    /**
     * Procesa el formulario de registro.
     */
    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => ['required', 'numeric', 'unique:usuarios,user_id'],
            'user_nombre'     => ['required', 'string', 'max:255'],
            'user_correo'     => ['required', 'email', 'max:255', 'unique:usuarios,user_correo'],
            'user_telefono'   => ['required', 'numeric'],
            'user_contrasena' => ['required', 'string', 'min:8'],
            'condiciones'     => ['accepted'],
        ]);

        // Crear usuario con Eloquent; el hash se aplica automáticamente
        // porque en el modelo User el campo user_contrasena está casteado como "hashed".
        User::create([
            'user_id'         => $validated['user_id'],
            'user_nombre'     => $validated['user_nombre'],
            'user_correo'     => $validated['user_correo'],
            'user_telefono'   => $validated['user_telefono'],
            'user_contrasena' => $validated['user_contrasena'],
            'rol_id'          => 3,
        ]);

        return redirect()
            ->route('inicio')
            ->with('status', '✔️ Registro guardado con éxito');
    }
}


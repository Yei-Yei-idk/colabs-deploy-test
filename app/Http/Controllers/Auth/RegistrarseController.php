<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

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
            'user_id'         => ['required', 'string', 'digits_between:6,12', 'unique:usuarios,user_id'],
            'user_nombre'     => ['required', 'string', 'max:255'],
            'user_correo'     => ['required', 'email', 'max:255', 'unique:usuarios,user_correo'],
            'user_telefono'   => ['required', 'numeric'],
            'user_contrasena' => ['required', 'string', 'min:8'],
            'condiciones'     => ['accepted'],
        ], [
            'user_id.required' => 'El número de documento es obligatorio.',
            'user_id.string' => 'El número de documento debe ser una cadena de texto.',
            'user_id.digits_between' => 'El número de documento debe tener entre 6 y 12 dígitos.',
            'user_id.unique' => 'Este número de documento ya está registrado.',
            'user_nombre.required' => 'El nombre es obligatorio.',
            'user_nombre.string' => 'El nombre debe ser una cadena de texto.',
            'user_nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'user_correo.required' => 'El correo electrónico es obligatorio.',
            'user_correo.email' => 'El correo electrónico debe tener un formato válido.',
            'user_correo.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'user_correo.unique' => 'Este correo electrónico ya está registrado.',
            'user_telefono.required' => 'El teléfono es obligatorio.',
            'user_telefono.numeric' => 'El teléfono debe ser un número.',
            'user_contrasena.required' => 'La contraseña es obligatoria.',
            'user_contrasena.string' => 'La contraseña debe ser una cadena de texto.',
            'user_contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'condiciones.accepted' => 'Debes aceptar los términos y condiciones.',
        ]);

        // Crear usuario con Eloquent; el hash se aplica automáticamente
        // porque en el modelo User el campo user_contrasena está casteado como "hashed".
        $usuario = User::create([
            'user_id'         => $validated['user_id'],
            'user_nombre'     => $validated['user_nombre'],
            'user_correo'     => $validated['user_correo'],
            'user_telefono'   => $validated['user_telefono'],
            'user_contrasena' => $validated['user_contrasena'],
            'rol_id'          => 3,
        ]);

        // Iniciar sesión con el usuario recién creado para que pueda entrar al dashboard bloqueado "verificar correo"
        Auth::login($usuario);

        // Disparar el evento de validación nativo de Laravel para forzar a que envíen el correo
        event(new Registered($usuario));

        // En lugar de redirigir a Login o Dashboard lo enviamos a la pantalla de "Espera"
        // (Aunque si no estuviera verificado el middleware verified lo atajaría de todos modos).
        return redirect()
            ->route('verification.notice')
            ->with('status', '✔️ Cuenta creada. Revisa tu correo electrónico para verificarla.');
    }
}


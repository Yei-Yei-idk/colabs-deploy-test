<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IniciarSesionController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function mostrarFormulario()
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión del usuario.
     */
    public function autenticar(Request $request)
    {
        $request->validate([
            'user'   => ['required', 'string'],
            'contra' => ['required', 'string'],
        ], [
            'user.required'   => 'El documento o correo es obligatorio.',
            'contra.required' => 'La contraseña es obligatoria.',
        ]);

        $loginInput = $request->input('user');
        $password   = $request->input('contra');

        // Buscar usuario por cédula (user_id) o por correo (user_correo)
        $usuario = User::where('user_id', $loginInput)
            ->orWhere('user_correo', $loginInput)
            ->first();

        if (! $usuario) {
            return back()
                ->withInput($request->only('user'))
                ->withErrors(['user' => 'Usuario no encontrado']);
        }

        // Intentar autenticar usando el correo del usuario y la contraseña proporcionada.
        // El modelo User ya define 'user_contrasena' como campo de contraseña (getAuthPasswordName).
        if (! Auth::attempt([
            'user_correo' => $usuario->user_correo,
            'password'    => $password,
        ], false)) {
            return back()
                ->withInput($request->only('user'))
                ->withErrors(['user' => 'Usuario o contraseña incorrectos']);
        }

        $request->session()->regenerate();

        // Redirección según rol, similar a tu lógica original
        switch ($usuario->rol_id) {
            case 3:
                // Cliente
                return redirect()->route('cliente.index')->with('status', '👋 ¡Bienvenido de nuevo!');

            case 1:
                // Administrador / Super usuario
                // TODO: Cambiar a la ruta real del dashboard cuando exista
                return redirect()->route('inicio')->with('status', '👋 ¡Bienvenido, Administrador!');
        }

        Auth::logout();

        return back()
            ->withInput($request->only('user'))
            ->withErrors(['user' => '⚠️ Rol no reconocido']);
    }
}


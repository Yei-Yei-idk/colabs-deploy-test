<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\VerifyEmailCustom;

class VerificacionController extends Controller
{
    /**
     * Muestra la vista de notificación.
     */
    public function notice(Request $request)
    {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->route('cliente.index');
        }
        return view('auth.verificar-correo');
    }

    /**
     * Genera un nuevo token, lo guarda y despacha el correo.
     * Implementa un contador de máximo 3 reenvíos por sesión.
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('cliente.index');
        }

        $intentos = Session::get('reenvios_verificacion', 0);
        
        if ($intentos >= 3) {
            return back()->with('error', 'Has superado el límite de reenvíos por ahora. Intenta más tarde.');
        }

        // Rotar Token + Expiración
        $token = Str::random(60);
        $user->verification_token = $token;
        $user->verification_token_expires_at = now()->addHour(); // Guardamos fecha de caducidad
        $user->save();

        // Enviar Correo Personalizado
        $user->notify(new VerifyEmailCustom($token));

        Session::put('reenvios_verificacion', $intentos + 1);

        return back()->with('status', 'verification-link-sent')->with('intentos', $intentos + 1);
    }

    /**
     * Valida el enlace firmado en el correo contra el token de la DB.
     * Enlace: /email/verify/{id}/{token}
     */
    public function verify(Request $request, $id, $token)
    {
        $user = User::find($id);

        if (!$user) {
            abort(404);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('cliente.index')->with('status', 'Tu correo ya estaba verificado.');
        }

        // Comparación estricta de tokens
        if (!$user->verification_token || $user->verification_token !== $token) {
            return redirect()->route('verification.notice')->with('error', 'El enlace es inválido o ha caducado. Por favor, solicita uno nuevo.');
        }

        // Validar Caducidad (24h)
        if ($user->verification_token_expires_at && now()->gt($user->verification_token_expires_at)) {
            return redirect()->route('verification.notice')->with('error', 'El enlace ha caducado. Por seguridad, los enlaces solo duran 1 hora. Por favor, solicita uno nuevo.');
        }

        // Marcar correo como verificado
        $user->email_verified_at = now();
        $user->verification_token = null; // Destruir token por seguridad
        $user->verification_token_expires_at = null;
        $user->save();

        Session::forget('reenvios_verificacion'); // Restablecer intentos

        return redirect()->route('cliente.index')->with('status', '¡Correo verificado con éxito! Bienvenido a Colabs.');
    }
}

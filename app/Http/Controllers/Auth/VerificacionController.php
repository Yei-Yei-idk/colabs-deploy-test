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
     * Si es la primera vez y no tiene token, envía automáticamente el correo.
     */
    public function notice(Request $request)
    {
        $user = $request->user();

        if ($user && $user->hasVerifiedEmail()) {
            return $user->rol_id == 1 || $user->rol_id == 2 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('cliente.index');
        }

        // Verificar límite de 30 segundos entre envíos
        $lastEmailSent = Session::get('last_email_sent_at');
        if ($lastEmailSent) {
            // Si es un objeto Carbon, convertir a timestamp en ms
            if ($lastEmailSent instanceof \Carbon\Carbon) {
                $lastEmailSentMs = $lastEmailSent->getTimestampMs();
            } else {
                $lastEmailSentMs = (int)$lastEmailSent;
            }
            
            $elapsedMs = now()->getTimestampMs() - $lastEmailSentMs;
            $elapsedSeconds = floor($elapsedMs / 1000);
            if ($elapsedSeconds < 30) {
                return view('auth.verificar-correo');
            }
        }

        // Si es la primera vez (sin token), enviar automáticamente
        if ($user && !$user->verification_token) {
            $token = Str::random(60);
            $user->verification_token = $token;
            $user->verification_token_expires_at = now()->addHour();
            $user->save();

            // Enviar Correo Personalizado
            $user->notify(new VerifyEmailCustom($token));

            // Guardar timestamp del envío en milisegundos
            Session::put('last_email_sent_at', now()->getTimestampMs());
            Session::put('reenvios_verificacion', 1); // Contar como primer intento
        }

        return view('auth.verificar-correo');
    }

    /**
     * Genera un nuevo token, lo guarda y despacha el correo.
     * Implementa un contador de máximo 3 reenvíos por sesión.
     * Verifica límite de 30 segundos entre envíos.
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $user->rol_id == 1 || $user->rol_id == 2 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('cliente.index');
        }

        // Verificar límite de 30 segundos entre envíos
        $lastEmailSent = Session::get('last_email_sent_at');
        if ($lastEmailSent) {
            // Si es un objeto Carbon, convertir a timestamp en ms
            if ($lastEmailSent instanceof \Carbon\Carbon) {
                $lastEmailSentMs = $lastEmailSent->getTimestampMs();
            } else {
                $lastEmailSentMs = (int)$lastEmailSent;
            }
            
            $elapsedMs = now()->getTimestampMs() - $lastEmailSentMs;
            $elapsedSeconds = floor($elapsedMs / 1000);
            if ($elapsedSeconds < 30) {
                $segundosRestantes = 30 - $elapsedSeconds;
                return back()->with('error', "Por favor, espera {$segundosRestantes} segundos antes de reenviar.");
            }
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

        // Guardar timestamp del envío
        Session::put('last_email_sent_at', now()->getTimestampMs());
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
            $dest = ($user->rol_id == 1 || $user->rol_id == 2) ? 'admin.dashboard' : 'cliente.index';
            return redirect()->route($dest)->with('status', 'Tu correo ya estaba verificado.');
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

        $dest = ($user->rol_id == 1 || $user->rol_id == 2) ? 'admin.dashboard' : 'cliente.index';
        return redirect()->route($dest)->with('status', '¡Correo verificado con éxito! Bienvenido a Colabs.');
    }

    /**
     * Muestra el formulario para cambiar correo
     */
    public function formCambiarCorreo(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('cliente.perfil');
        }

        // Marcar que se está accediendo a esta página desde el flujo correcto
        Session::put('cambiar_correo_access', true);

        return view('auth.cambiar-correo', compact('user'));
    }

    /**
     * Cambia el correo electrónico del usuario y lo requiere verificar nuevamente
     */
    public function cambiarCorreo(Request $request)
    {
        $user = $request->user();

        // Verificar que se accedió desde el flujo correcto
        if (!Session::has('cambiar_correo_access')) {
            return redirect()->route('verification.notice');
        }

        // Verificar límite de 30 segundos entre envíos
        $lastEmailSent = Session::get('last_email_sent_at');
        if ($lastEmailSent) {
            // Si es un objeto Carbon, convertir a timestamp en ms
            if ($lastEmailSent instanceof \Carbon\Carbon) {
                $lastEmailSentMs = $lastEmailSent->getTimestampMs();
            } else {
                $lastEmailSentMs = (int)$lastEmailSent;
            }
            
            $elapsedMs = now()->getTimestampMs() - $lastEmailSentMs;
            $elapsedSeconds = floor($elapsedMs / 1000);
            if ($elapsedSeconds < 30) {
                $segundosRestantes = 30 - $elapsedSeconds;
                return back()->with('error', "Por favor, espera {$segundosRestantes} segundos antes de cambiar el correo nuevamente.");
            }
        }

        $request->validate([
            'correo_nuevo' => 'required|email|unique:usuarios,user_correo|max:100',
            'correo_confirmacion' => 'required|same:correo_nuevo'
        ], [
            'correo_nuevo.required' => 'El correo es obligatorio.',
            'correo_nuevo.email' => 'El formato del correo es inválido.',
            'correo_nuevo.unique' => 'Este correo electrónico ya está registrado.',
            'correo_confirmacion.same' => 'Los correos no coinciden.'
        ]);

        // Actualizar correo y resetear verificación usando update
        User::where('user_id', $user->user_id)->update([
            'user_correo' => $request->correo_nuevo,
            'email_verified_at' => null,
            'verification_token' => Str::random(60),
            'verification_token_expires_at' => now()->addHour()
        ]);

        // Recargar el usuario actualizado
        $user = $user->fresh();

        // Enviar correo de verificación al nuevo correo
        $user->notify(new VerifyEmailCustom($user->verification_token));

        // Guardar timestamp del envío y resetear contador de reenvíos
        Session::put('last_email_sent_at', now()->getTimestampMs());
        Session::forget('reenvios_verificacion');
        Session::put('reenvios_verificacion', 1); // Contar como primer intento

        // Olvidar el flag de acceso y redirigir a verificación
        Session::forget('cambiar_correo_access');

        return redirect()->route('verification.notice')->with('status', 'verification-email-changed')->with('success', '✅ Se ha enviado un enlace de verificación a tu nuevo correo. Por favor revisa tu bandeja de entrada.');
    }
}

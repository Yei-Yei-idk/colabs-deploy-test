<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RecuperarContrasenaController extends Controller
{
    /**
     * Envía un enlace de restablecimiento de contraseña al correo del usuario.
     *
     * POST /forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Introduce un correo electrónico válido.',
            'email.max'      => 'El correo es demasiado largo.',
        ]);

        $email = mb_strtolower(trim($request->email));
        $user  = User::where('user_correo', $email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'No encontramos un usuario con ese correo.',
            ])->withInput();
        }

        // Throttle: no reenviar antes de 60 segundos
        $recentToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if ($recentToken && Carbon::parse($recentToken->created_at)->addSeconds(60)->isFuture()) {
            $segundosRestantes = (int) now()->diffInSeconds(Carbon::parse($recentToken->created_at)->addSeconds(60));
            return back()->withErrors([
                'email' => "Espera {$segundosRestantes} segundos antes de solicitar otro enlace.",
            ])->withInput();
        }

        // Generar token
        $token = Str::random(64);

        // Guardar/actualizar token en la tabla
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token'      => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Construir el enlace de restablecimiento
        $resetUrl = url("/restablecer-contrasena?token={$token}&email=" . urlencode($email));

        // Enviar correo con manejo de errores
        try {
            Mail::send('emails.restablecer-contrasena', ['resetUrl' => $resetUrl, 'user' => $user], function ($message) use ($user) {
                $message->to($user->user_correo)
                        ->subject('Restablecer contraseña - ' . config('app.name'));
            });
        } catch (\Exception $e) {
            Log::error('Error enviando correo de restablecimiento: ' . $e->getMessage());

            // Limpiar el token generado para que pueda reintentar
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return back()->withErrors([
                'email' => 'No pudimos enviar el correo. Intenta de nuevo más tarde.',
            ])->withInput();
        }

        return back()->with('status', '¡Enlace de restablecimiento enviado! Revisa tu correo (incluida la carpeta de spam).');
    }

    /**
     * Muestra el formulario de restablecimiento de contraseña.
     *
     * GET /reset-password?token=...&email=...
     */
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        // Validar que lleguen los parámetros necesarios
        if (! $token || ! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Enlace de restablecimiento incompleto. Solicita uno nuevo.']);
        }

        // Verificar que exista un registro para ese email
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace ya fue utilizado o no es válido. Solicita uno nuevo.']);
        }

        // Verificar que no haya expirado
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace ha expirado. Solicita uno nuevo.']);
        }

        // Verificar que el token sea correcto
        if (! Hash::check($token, $record->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'El enlace de restablecimiento no es válido. Solicita uno nuevo.']);
        }

        return view('auth.restablecer-contrasena', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Restablece la contraseña del usuario con el token recibido.
     *
     * POST /reset-password
     */
    public function resetPassword(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'token'    => ['required', 'string', 'size:64'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', 'min:6', 'max:100'],
        ], [
            'token.required'      => 'Falta el token de verificación.',
            'token.size'          => 'El token de verificación no es válido.',
            'email.required'      => 'El correo es obligatorio.',
            'email.email'         => 'Introduce un correo válido.',
            'password.required'   => 'La nueva contraseña es obligatoria.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
            'password.min'        => 'La contraseña debe tener al menos 6 caracteres.',
            'password.max'        => 'La contraseña no puede tener más de 100 caracteres.',
        ]);

        if ($validator->fails()) {
            // Si el usuario intentó forzar el formulario borrando los inputs hidden "token" o "email"
            if (!$request->token || !$request->email) {
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'El enlace de restablecimiento está corrupto. Solicita uno nuevo.']);
            }
            
            // Forzar redirect a la ruta GET exacta con sus parámetros. Esto previene que 
            // navegadores que bloquean el HTTP Referer envíen a los usuarios a un error 500.
            return redirect()->route('password.reset', [
                'token' => $request->token,
                'email' => $request->email,
            ])->withErrors($validator)->withInput();
        }

        $email = mb_strtolower(trim($request->email));

        // Buscar el token en la base de datos
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'No encontramos una solicitud de restablecimiento. Solicita un nuevo enlace.']);
        }

        // Verificar que el token no ha expirado (60 minutos)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace ha expirado. Solicita uno nuevo.']);
        }

        // Verificar el token
        if (! Hash::check($request->token, $record->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'El enlace de restablecimiento no es válido. Solicita uno nuevo.']);
        }

        // Buscar usuario
        $user = User::where('user_correo', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'No encontramos un usuario con ese correo.']);
        }

        // Actualizar la contraseña
        $user->forceFill([
            'user_contrasena' => Hash::make($request->password),
        ])->save();

        // Eliminar el token usado
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('status', '¡Contraseña restablecida con éxito! Inicia sesión con tu nueva contraseña.');
    }
}

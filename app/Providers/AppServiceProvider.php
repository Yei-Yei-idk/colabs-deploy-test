<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Reserva;
use App\Observers\ReservaObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Reserva::observe(ReservaObserver::class);
        
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verifica tu correo - ' . config('app.name'))
                ->view('emails.verificar-correo', ['url' => $url, 'user' => $notifiable]);
        });

        \Illuminate\Support\Facades\View::composer('layouts.cliente', function ($view) {
            $user_id = 1; // Temporal (será auth()->id() luego)
            
            // Obtener las últimas 5 reservas del usuario que no sean Pendientes (ya que esas son creación suya) 
            // o simplemente las últimas reservas generales como notificaciones
            $notificaciones = \App\Models\Reserva::with('espacio')
                ->where('user_id', $user_id)
                ->orderBy('reserva_id', 'DESC')
                ->take(5)
                ->get();

            $view->with('notificaciones', $notificaciones);
        });
    }
}

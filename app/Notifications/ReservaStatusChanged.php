<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reserva;

class ReservaStatusChanged extends Notification
{
    use Queueable;

    public $reserva;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reserva $reserva, string $status)
    {
        $this->reserva = $reserva;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $espacioNombre = $this->reserva->espacio->esp_nombre ?? 'N/D';
        $reservaId = $this->reserva->reserva_id;

        $subject = "";
        $greeting = "Hola, " . ($notifiable->first_name ?? $notifiable->user_nombre) . "!";

        switch ($this->status) {
            case 'Pendiente':
                $subject = "⏳ Solicitud para [{$espacioNombre}] en proceso — Co-Labs";
                break;
            case 'Aceptada':
                $subject = "✅ ¡Tu reserva en [{$espacioNombre}] ha sido aceptada! — Co-Labs";
                break;
            case 'Rechazada':
                $subject = "⚠️ Información sobre tu reserva en [{$espacioNombre}] — Co-Labs";
                break;
            case 'Cancelada':
                $subject = "🚫 Confirmación de cancelación en [{$espacioNombre}] — Co-Labs";
                break;
            case 'Finalizada':
                $subject = "🌟 ¿Qué te pareció [{$espacioNombre}]? — Co-Labs";
                break;
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->view('emails.reservas', [
                'reserva' => $this->reserva,
                'status' => $this->status,
                'user' => $notifiable
            ]);
    }
}

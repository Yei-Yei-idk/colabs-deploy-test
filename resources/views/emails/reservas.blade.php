<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Reserva — Co-Labs</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f2; font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif; -webkit-font-smoothing:antialiased;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f2; padding:40px 16px;">
        <tr>
            <td align="center">

                <!-- Main Card (Expansive 680px) -->
                <table role="presentation" width="680" cellpadding="0" cellspacing="0" style="width:100%; max-width:680px; background-color:#ffffff; border-radius:24px; overflow:hidden; box-shadow: 0 12px 40px rgba(0,0,0,0.08);">

                    <!-- ═══ Header ═══ -->
                    <tr>
                        <td style="background:#000000; padding:32px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left">
                                        <img src="{{ $message->embed(public_path('ASSETS/logo.png')) }}" alt="Colabs Logo" height="32" style="display:block; border:none;" />
                                    </td>
                                    <td align="right" style="color:#ffffff; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1px; opacity:0.8;">
                                        Notificación Oficial
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ═══ Yellow accent line ═══ -->
                    <tr>
                        <td style="background: linear-gradient(90deg, #facc15, #eab308); height:6px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- ═══ Body (Horizontal Layout) ═══ -->
                    <tr>
                        <td style="padding:48px 40px 20px;">
                            
                            <!-- Main Horizontal Wrapper (using align left/right for responsiveness) -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:top;">
                                        
                                        <!-- Column 1: Message (approx 60%) -->
                                        <table role="presentation" align="left" width="340" cellpadding="0" cellspacing="0" style="width:100%; max-width:340px;">
                                            <tr>
                                                <td style="padding-bottom:12px;">
                                                    <div style="width:48px; height:48px; border-radius:12px; background:rgba(250,204,21,0.15); text-align:center; line-height:48px; font-size:24px; margin-bottom:16px;">
                                                        @if($status == 'Pendiente') ⏳ @elseif($status == 'Aceptada') ✅ @elseif($status == 'Rechazada') ⚠️ @elseif($status == 'Cancelada') 🚫 @else 🌟 @endif
                                                    </div>
                                                    <h2 style="margin:0 0 12px; color:#111827; font-size:26px; font-weight:800; letter-spacing:-0.5px; line-height:1.2;">
                                                        @if($status == 'Pendiente') Solicitud recibida @elseif($status == 'Aceptada') ¡Reserva aceptada! @elseif($status == 'Rechazada') Solicitud rechazada @elseif($status == 'Cancelada') Reserva cancelada @else ¡Reserva finalizada! @endif
                                                    </h2>
                                                    <p style="color:#4b5563; font-size:15px; line-height:1.6; margin:0 0 24px;">
                                                        @if($status == 'Pendiente')
                                                            Hola <strong>{{ $user->user_nombre }}</strong>, hemos recibido tu solicitud para <strong>{{ $reserva->espacio->esp_nombre }}</strong>. Nos contactaremos contigo en menos de 24h para el pago.
                                                        @elseif($status == 'Aceptada')
                                                            ¡Buenas noticias, <strong>{{ $user->user_nombre }}</strong>! Tu reserva para <strong>{{ $reserva->espacio->esp_nombre }}</strong> ha sido confirmada.
                                                        @elseif($status == 'Rechazada')
                                                            Hola <strong>{{ $user->user_nombre }}</strong>, lamentamos informarte que la solicitud para <strong>{{ $reserva->espacio->esp_nombre }}</strong> no procedió.
                                                        @elseif($status == 'Cancelada')
                                                            Hola <strong>{{ $user->user_nombre }}</strong>, confirmamos la cancelación de tu reserva en <strong>{{ $reserva->espacio->esp_nombre }}</strong>.
                                                        @else
                                                            ¡Hola <strong>{{ $user->user_nombre }}</strong>! Esperamos que hayas disfrutado tu tiempo en <strong>{{ $reserva->espacio->esp_nombre }}</strong>.
                                                        @endif
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Column 2: Details Card (approx 40%) -->
                                        <table role="presentation" align="right" width="230" cellpadding="0" cellspacing="0" style="width:100%; max-width:230px;">
                                            <tr>
                                                <td style="padding-top:10px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border-radius:16px; border:1px solid #e2e8f0; border-bottom:4px solid #eab308;">
                                                        <tr>
                                                            <td style="padding:20px;">
                                                                <h4 style="margin:0 0 16px; font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:1px; font-weight:700;">Reserva #{{ $reserva->reserva_id }}</h4>
                                                                
                                                                <div style="margin-bottom:12px;">
                                                                    <div style="font-size:11px; color:#94a3b8; margin-bottom:2px;">Fecha</div>
                                                                    <div style="font-size:14px; color:#1e293b; font-weight:600;">{{ \Carbon\Carbon::parse($reserva->rsva_fecha)->format('d/m/Y') }}</div>
                                                                </div>

                                                                <div style="margin-bottom:12px;">
                                                                    <div style="font-size:11px; color:#94a3b8; margin-bottom:2px;">Horario</div>
                                                                    <div style="font-size:14px; color:#1e293b; font-weight:600;">{{ \Carbon\Carbon::parse($reserva->rsva_hora_inicio)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reserva->rsva_hora_fin)->format('g:i A') }}</div>
                                                                </div>

                                                                <div>
                                                                    <div style="font-size:11px; color:#94a3b8; margin-bottom:2px;">Espacio</div>
                                                                    <div style="font-size:14px; color:#eab308; font-weight:700;">{{ $reserva->espacio->esp_nombre }}</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- ═══ CTA Section ═══ -->
                    <tr>
                        <td style="padding:0 40px 48px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left">
                                        @if($status == 'Finalizada')
                                            <a href="{{ route('cliente.detalles_reserva', $reserva->reserva_id) }}" 
                                               style="display:inline-block; background:#000000; color:#ffffff; text-decoration:none; padding:16px 36px; border-radius:12px; font-size:15px; font-weight:700; letter-spacing:0.3px;">
                                                Calificar experiencia →
                                            </a>
                                        @else
                                            <a href="{{ route('cliente.detalles_reserva', $reserva->reserva_id) }}" 
                                               style="display:inline-block; background:#facc15; color:#000000; text-decoration:none; padding:16px 36px; border-radius:12px; font-size:15px; font-weight:700; letter-spacing:0.3px;">
                                                Gestionar mi reserva
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ═══ Footer ═══ -->
                    <tr>
                        <td style="background:#000000; padding:32px 40px; text-align:center;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left" style="color:#64748b; font-size:12px;">
                                        © {{ date('Y') }} <span style="color:#facc15; font-weight:700;">Co•labs</span> Platform.<br>
                                        Tu espacio de trabajo inteligente.
                                    </td>
                                    <td align="right" style="color:#475569; font-size:12px;">
                                        <a href="#" style="color:#facc15; text-decoration:none;">Términos</a> · 
                                        <a href="#" style="color:#facc15; text-decoration:none;">Contacto</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>

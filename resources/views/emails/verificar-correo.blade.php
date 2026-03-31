<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f2; font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif; -webkit-font-smoothing:antialiased;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f2; padding:40px 16px;">
        <tr>
            <td align="center">

                <!-- Main Card -->
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.1);">

                    <!-- ═══ Header: Black bar + Logo ═══ -->
                    <tr>
                        <td style="background:#000000; padding:28px 32px; text-align:center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="vertical-align:middle; text-align:center;">
                                        <img src="{{ $message->embed(public_path('ASSETS/logo.png')) }}" alt="Colabs Logo" height="36" style="display:block; margin:0 auto; max-width:100%; border:none;" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ═══ Yellow accent line ═══ -->
                    <tr>
                        <td style="background: linear-gradient(90deg, #facc15, #eab308); height:4px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- ═══ Body ═══ -->
                    <tr>
                        <td style="padding:36px 32px 16px;">

                            <!-- Icon -->
                            <div style="text-align:center; margin-bottom:20px;">
                                <div style="width:56px; height:56px; border-radius:50%; background:rgba(250,204,21,0.12); display:inline-block; text-align:center; line-height:56px; font-size:26px;">✉️</div>
                            </div>

                            <!-- Title -->
                            <h2 style="margin:0 0 8px; color:#111827; font-size:22px; font-weight:800; text-align:center; letter-spacing:-0.3px;">
                                ¡Casi listo!
                            </h2>

                            <p style="color:#6b7280; font-size:14px; line-height:1.7; margin:0 0 28px; text-align:center;">
                                Hola, <strong style="color:#111827;">{{ $user->user_nombre }}</strong>. Aseguremos que tienes acceso a tu correo. Por favor haz clic en el botón de abajo para verificar tu cuenta en <strong style="color:#111827;">Co-Labs</strong>.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:0 0 28px;">
                                        <a href="{{ $url }}" 
                                           style="display:inline-block; background:linear-gradient(90deg, #facc15, #eab308); color:#000000; text-decoration:none; padding:14px 40px; border-radius:999px; font-size:15px; font-weight:700; letter-spacing:0.2px; box-shadow: 0 10px 22px rgba(250,204,21,0.35);">
                                            Verificar correo electrónico
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-top:1px solid #e5e7eb; padding-top:20px;">
                                        
                                        <!-- Info box -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb; border-radius:12px; border:1px solid #e5e7eb;">
                                            <tr>
                                                <td style="padding:16px 18px;">
                                                    <p style="color:#9ca3af; font-size:12px; line-height:1.5; margin:0;">
                                                        Si no creaste esta cuenta, simplemente ignora o elimina este mensaje.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ═══ Link fallback ═══ -->
                    <tr>
                        <td style="padding:0 32px 28px;">
                            <p style="color:#9ca3af; font-size:11px; line-height:1.5; margin:0; word-break:break-all;">
                                Si el botón no funciona, copia y pega este enlace:<br>
                                <a href="{{ $url }}" style="color:#eab308;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- ═══ Footer ═══ -->
                    <tr>
                        <td style="background:#000000; padding:20px 32px; text-align:center;">
                            <p style="color:#6b7280; font-size:11px; margin:0 0 4px;">
                                © {{ date('Y') }} <span style="color:#facc15; font-weight:700;">Co-Labs</span> · Todos los derechos reservados
                            </p>
                            <p style="color:#4b5563; font-size:10px; margin:0;">
                                Acceso de seguridad verificado · Colabs Platform
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>

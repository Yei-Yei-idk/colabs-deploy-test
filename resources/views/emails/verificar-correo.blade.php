<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo — Co-Labs</title>
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
                                    <td align="center">
                                        <img src="{{ $message->embed(public_path('ASSETS/logo.png')) }}" alt="Colabs Logo" height="36" style="display:block; border:none;" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ═══ Yellow accent line ═══ -->
                    <tr>
                        <td style="background: linear-gradient(90deg, #facc15, #eab308); height:6px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- ═══ Body (Expansive Hero) ═══ -->
                    <tr>
                        <td style="padding:56px 60px 40px; text-align:center;">
                            
                            <!-- Icon -->
                            <div style="width:64px; height:64px; border-radius:16px; background:rgba(250,204,21,0.15); display:inline-block; text-align:center; line-height:64px; font-size:28px; margin-bottom:24px;">
                                📩
                            </div>

                            <!-- Title -->
                            <h1 style="margin:0 0 16px; color:#111827; font-size:32px; font-weight:800; letter-spacing:-1px; line-height:1.1;">
                                Verifica tu cuenta
                            </h1>

                            <!-- Message -->
                            <p style="color:#4b5563; font-size:16px; line-height:1.7; margin:0 0 40px; max-width:500px; margin-left:auto; margin-right:auto;">
                                Hola, <strong style="color:#111827;">{{ $user->user_nombre }}</strong>. Estamos emocionados de tenerte en <span style="font-weight:700; color:#eab308;">Co•labs</span>. Solo falta este paso para asegurar tu acceso y proteger tu cuenta.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:0 0 24px;">
                                        <a href="{{ $url }}" 
                                           style="display:inline-block; background:linear-gradient(90deg, #facc15, #eab308); color:#000000; text-decoration:none; padding:18px 48px; border-radius:12px; font-size:16px; font-weight:800; letter-spacing:0.5px; box-shadow: 0 12px 24px rgba(234,179,8,0.3);">
                                            Confirmar correo electrónico
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Simple Footer-Info -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #f1f5f9; padding-top:24px;">
                                <tr>
                                    <td align="center">
                                        <p style="color:#94a3b8; font-size:12px; line-height:1.5; margin:0; max-width:400px;">
                                            Si el botón no funciona, copia este enlace en tu navegador:<br>
                                            <a href="{{ $url }}" style="color:#eab308; text-decoration:none;">{{ $url }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- ═══ Footer ═══ -->
                    <tr>
                        <td style="background:#000000; padding:32px 40px; text-align:center;">
                            <p style="color:#64748b; font-size:12px; margin:0 0 8px;">
                                © {{ date('Y') }} <span style="color:#facc15; font-weight:700;">Co•labs</span> Platform. Tu espacio de trabajo premium.
                            </p>
                            <p style="color:#475569; font-size:10px; margin:0; text-transform:uppercase; letter-spacing:1px;">
                                Seguridad verificada · Colombia
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>

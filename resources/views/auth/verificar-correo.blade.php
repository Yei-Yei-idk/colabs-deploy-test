@extends('layouts.app')

@section('title', 'Verifica tu correo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/verificacion.css') }}">
@endpush

@section('content')
    <section class="isolated-verify-container" 
             data-verificacion-config 
             data-last-email-sent="{{ session('last_email_sent_at') ?? 0 }}">
             
        <div class="verify-card-premium">
            <!-- Body -->
            <div class="card-body">
                <div class="icon-wrapper">✉️</div>
                
                <h2 class="card-title">Verifica tu correo electrónico</h2>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert-box alert-success">
                        <p class="alert-text">
                            🔥 <strong>¡Enviado!</strong> Se ha enviado un nuevo enlace de un solo uso. Revisa tu bandeja de entrada.
                        </p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-box alert-error">
                        <p class="alert-text">
                            🛡️ <strong>Aviso:</strong> {{ session('error') }}
                        </p>
                    </div>
                @endif

                <p class="card-text">
                    La confidencialidad es nuestra prioridad. Por favor confirma la propiedad de tu cuenta haciendo clic en el enlace que enviamos a <strong>{{ auth()->user()->user_correo }}</strong>.
                </p>

                @if (session('status') == 'verification-email-changed')
                    <div class="alert-box alert-success">
                        <p class="alert-text">
                            ✅ Se ha enviado un nuevo enlace de verificación a tu nuevo correo. Por favor revisa tu bandeja de entrada.
                        </p>
                    </div>
                @endif

                @php
                    $intentosReales = session('intentos') ?? \Illuminate\Support\Facades\Session::get('reenvios_verificacion', 0);
                @endphp

                @if ($intentosReales >= 3)
                    <button type="button" class="btn-gold-pill" disabled style="background: #e5e7eb; color: #9ca3af; box-shadow: none;">
                        Límite máximo alcanzado 🔒
                    </button>
                    <p style="color: #ef4444; font-size: 12px; font-weight: 600; margin-top: -10px; margin-bottom: 20px;">
                        Por seguridad tu cuenta está en enfriamiento. Recarga más tarde.
                    </p>
                @else
                    <form action="{{ route('verification.send') }}" method="POST" id="form-reenviar">
                        @csrf
                        <button type="submit" id="btn-reenviar" class="btn-gold-pill">
                            Reenviar llave de acceso
                        </button>
                    </form>
                @endif

                <!-- Botón para cambiar correo -->
                <a href="{{ route('verification.form-cambiar-correo') }}" class="btn-change-email">
                    ✏️ Cambiar correo
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-ghost">
                        Cerrar sesión y volver al inicio
                    </button>
                </form>

                <p style="margin-top: 32px; color: #9ca3af; font-size: 11px;">
                    © {{ date('Y') }} <span style="font-weight:700;">Co-Labs</span> · Acceso de seguridad verificado
                </p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth/verificacion.js') }}"></script>
@endpush


@extends('layouts.cliente')

@section('title', 'Dashboard de Perfil — Co-Labs')

@section('content')
<section id="perfil-dashboard" class="zero-scroll-container animate-fade-in">
    
    <!-- ═══ DASHBOARD MAESTRO ═══ -->
    <div class="dash-perfil-wrapper">
        
        <!-- BARRA IZQUIERDA: Identidad y Estado -->
        <div class="dash-sidebar">
            <div class="user-id-card">
                <div class="avatar-dash {{ $usuario->avatar_color ?? 'blue' }}">
                    {{ $usuario->avatar_initial ?? 'U' }}
                    <span class="badge-status-dot {{ $usuario->email_verified_at ? 'verified' : 'pending' }}"></span>
                </div>
                <h2>{{ $usuario->user_nombre }}</h2>
                <div class="id-tag">Miembro de Co•Labs</div>
                
                @if(!$usuario->email_verified_at)
                    <div class="alert-verification-warning">
                        <span>⚠️ Cuenta no verificada</span>
                        <p>Por favor confirma tu correo o corrígelo en el formulario.</p>
                    </div>
                @endif
            </div>

            <div class="dash-stats">
                <div class="stat-bubble">
                    <span class="count">{{ count($usuario->reservas ?? []) }}</span>
                    <span class="label">Reservas</span>
                </div>
                <div class="stat-bubble">
                    <span class="count">★</span>
                    <span class="label">Activo</span>
                </div>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL: Información Expandida (Sin Scroll) -->
        <div class="dash-main-content">
            
            @if(session('success'))
                <div class="dash-alert success animate-slide-in">
                    <span class="icon">✅</span> {{ session('success') }}
                </div>
            @endif

            <form id="formPerfil" method="POST" action="{{ route('cliente.perfil.actualizar') }}" class="dash-form">
                @csrf
                
                <div class="dash-sections-grid">
                    
                    <!-- Bloque: Contacto -->
                    <div class="dash-card-section">
                        <div class="section-title">
                            <span class="icon">👤</span>
                            <h3>Datos de Contacto</h3>
                        </div>
                        
                        <div class="form-row-dash">
                            <div class="field-dash">
                                <label for="nombre">Nombre Completo</label>
                                <input type="text" id="nombre" name="nombre" value="{{ $usuario->user_nombre }}" required>
                            </div>
                        </div>
                        
                        <div class="form-row-dash multi-col">
                            <div class="field-dash">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" id="email" name="email" value="{{ $usuario->user_correo }}" required>
                            </div>
                            <div class="field-dash">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" value="{{ $usuario->user_telefono }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Bloque: Seguridad -->
                    <div class="dash-card-section security">
                        <div class="section-title">
                            <span class="icon">🔒</span>
                            <h3>Seguridad</h3>
                        </div>
                        
                        <div class="form-row-dash multi-col">
                            <div class="field-dash">
                                <label for="password">Contraseña Actual</label>
                                <input type="password" id="password" name="password" placeholder="••••••••">
                            </div>
                            <div class="field-dash">
                                <label for="newpassword">Nueva Contraseña</label>
                                <input type="password" id="newpassword" name="newpassword" placeholder="••••••••">
                            </div>
                        </div>
                        <div class="security-note">
                            Cambia tu contraseña periódicamente para mantener tu cuenta segura.
                        </div>
                    </div>

                </div>

                <!-- Footer de Acciones (Fijo abajo en el dash) -->
                <div class="dash-form-actions">
                    <button type="submit" class="btn-save-dash btn-guardar">
                        Guardar Cambios del Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>
@endsection

@section('scripts')
<script src="{{ asset('js/cliente/perfil.js') }}"></script>
@endsection

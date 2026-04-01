@extends('layouts.app')

@section('title', 'Cambiar Correo Electrónico')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/verificacion.css') }}">
@endpush

@section('content')
    <section class="isolated-verify-container">
        <div class="verify-card-premium">
            <!-- Body -->
            <div class="card-body">
                <h2 class="card-title">Actualizar tu correo electrónico</h2>
                
                <p class="card-text">
                    Ingresa tu nuevo correo electrónico. Después deberás verificarlo para continuar usando tu cuenta.
                </p>

                <!-- Mostrar errores si los hay -->
                @if ($errors->any())
                    <div class="alert-box alert-error">
                        <p class="alert-text">
                            ❌ <strong>Error:</strong> {{ $errors->first() }}
                        </p>
                    </div>
                @endif

                <form action="{{ route('verification.cambiar-correo') }}" method="POST" class="form-cambiar-correo">
                    @csrf

                    <!-- Correo actual (solo lectura) -->
                    <div class="form-group">
                        <label class="form-label">Correo Actual</label>
                        <input 
                            type="email" 
                            value="{{ $user->user_correo }}"
                            disabled
                            class="form-input-disabled">
                    </div>

                    <!-- Nuevo Correo -->
                    <div class="form-group">
                        <label for="correo_nuevo" class="form-label">Nuevo Correo</label>
                        <input 
                            type="email" 
                            id="correo_nuevo" 
                            name="correo_nuevo" 
                            placeholder="tu-nuevo-correo@example.com"
                            required
                            autofocus
                            class="form-input">
                    </div>

                    <!-- Confirmar Correo -->
                    <div class="form-group form-group-last">
                        <label for="correo_confirmacion" class="form-label">Confirmar Correo</label>
                        <input 
                            type="email" 
                            id="correo_confirmacion" 
                            name="correo_confirmacion" 
                            placeholder="tu-nuevo-correo@example.com"
                            required
                            class="form-input">
                    </div>

                    <!-- Botones -->
                    <div class="form-buttons">
                        <button type="submit" class="form-button-submit">
                            ✅ Actualizar Correo
                        </button>
                        <a href="{{ route('verification.notice') }}" class="form-button-back">
                            ← Volver
                        </a>
                    </div>
                </form>

                <p class="form-footer">
                    © {{ date('Y') }} <span style="font-weight:700;">Co-Labs</span> · Acceso seguro
                </p>
            </div>
        </div>
    </section>
@endsection

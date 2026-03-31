@extends('layouts.app')

@section('title', '¿Olvidaste tu contraseña?')

@section('content')
    <section class="auth-registrarse">
        <div class="formulario">
            <h1>Recuperar contraseña</h1>

            {{-- Mensaje de éxito --}}
            @if (session('status'))
                <div class="exito">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Errores --}}
            @if ($errors->any())
                <div class="errores">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="instrucciones">
                Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>

            <form action="{{ route('password.email') }}" method="post">
                @csrf

                <input
                    type="email"
                    name="email"
                    placeholder="Correo electrónico"
                    class="mi-input"
                    value="{{ old('email') }}"
                    required
                >
                <br><br>

                <button type="submit" class="btn-login">
                    Enviar enlace
                </button>
                <br>

                <p>
                    <a href="{{ route('login') }}">Volver a iniciar sesión</a>
                </p>
            </form>
        </div>
    </section>
@endsection

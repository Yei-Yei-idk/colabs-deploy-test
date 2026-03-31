@extends('layouts.app')

@section('title', 'Restablecer contraseña')

@section('content')
    <section class="auth-registrarse">
        <div class="formulario">
            <h1>Nueva contraseña</h1>

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

            <form action="{{ route('password.update') }}" method="post">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <input
                    type="password"
                    name="password"
                    placeholder="Nueva contraseña"
                    class="mi-input"
                    required
                    minlength="6"
                >
                <br>

                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirmar contraseña"
                    class="mi-input"
                    required
                    minlength="6"
                >
                <br><br>

                <button type="submit" class="btn-login">
                    Restablecer contraseña
                </button>
                <br>

                <p>
                    <a href="{{ route('login') }}">Volver a iniciar sesión</a>
                </p>
            </form>
        </div>
    </section>
@endsection

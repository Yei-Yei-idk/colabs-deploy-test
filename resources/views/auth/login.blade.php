@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <section class="auth-registrarse">
        <div class="formulario">
            <h1>Iniciar sesión</h1>

            {{-- Errores de validación --}}
            @if ($errors->any())
                <div class="errores">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form action="{{ route('login.autenticar') }}" method="post">
                @csrf

                <input
                    type="text"
                    name="user"
                    placeholder="Documento o Correo"
                    class="mi-input"
                    value="{{ old('user') }}"
                    
                >
                <br>

                <input
                    type="password"
                    name="contra"
                    placeholder="Contraseña"
                    class="mi-input"
                    
                >
                <div class="auth-remember-wrapper">
                    <label class="auth-label-checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Recordarme en este equipo
                    </label>
                </div>

                <button type="submit" name="login" class="btn-login">
                    Ingresar
                </button>
                <br>

                <p>
                    <a href="{{ route('password.request') }}">¿Has olvidado tu contraseña?</a>
                </p>
            </form>
        </div>
    </section>
@endsection


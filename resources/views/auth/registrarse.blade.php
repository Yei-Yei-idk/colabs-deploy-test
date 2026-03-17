@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
    <section class="auth-registrarse">
    <div class="formulario">
        <h1>Registrarse</h1>

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

        <form action="{{ route('registrarse.guardar') }}" method="post">
            @csrf

            <input
                type="number"
                name="user_id"
                id="user_id"
                placeholder="Cédula"
                class="mi-input"
                value="{{ old('user_id') }}"
            >
            <br>

            <input
                type="text"
                name="user_nombre"
                id="user_nombre"
                placeholder="Nombre"
                class="mi-input"
                value="{{ old('user_nombre') }}"

            >
            <br>

            <input
                type="email"
                name="user_correo"
                id="user_correo"
                placeholder="Correo"
                class="mi-input"
                value="{{ old('user_correo') }}"
            >
            <br>

            <input
                type="number"
                name="user_telefono"
                id="user_telefono"
                placeholder="Telefono"
                class="mi-input"
                value="{{ old('user_telefono') }}"
            >
            <br>

            <input
                type="password"
                name="user_contrasena"
                id="user_contrasena"
                placeholder="Contraseña"
                class="mi-input"
            >
            <br><br>

            <input
                type="checkbox"
                name="condiciones"
                id="term_cond"
                {{ old('condiciones') ? 'checked' : '' }}
            >
            <p>Al crear la cuenta aceptas nuestros términos y condiciones.</p>

            <button type="submit" name="crear" class="btn-login" style="cursor:pointer;">Crear</button>
        </form>
    </div>
    </section>
@endsection


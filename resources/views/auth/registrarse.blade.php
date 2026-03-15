@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
    <section class="auth-registrarse">
    <div class="formulario">
        <h1>Registrarse</h1>

        {{-- Mensaje de éxito tipo snackbar usando flash session --}}
        <div id="snackbar">
            @if (session('status'))
                {{ session('status') }}
            @endif
        </div>

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
                required
            >
            <br>

            <input
                type="text"
                name="user_nombre"
                id="user_nombre"
                placeholder="Nombre"
                class="mi-input"
                value="{{ old('user_nombre') }}"
                required
            >
            <br>

            <input
                type="email"
                name="user_correo"
                id="user_correo"
                placeholder="Correo"
                class="mi-input"
                value="{{ old('user_correo') }}"
                required
            >
            <br>

            <input
                type="number"
                name="user_telefono"
                id="user_telefono"
                placeholder="Telefono"
                class="mi-input"
                value="{{ old('user_telefono') }}"
                required
            >
            <br>

            <input
                type="password"
                name="user_contrasena"
                id="user_contrasena"
                placeholder="Contraseña"
                class="mi-input"
                required
            >
            <br><br>

            <input
                type="checkbox"
                name="condiciones"
                id="term_cond"
                {{ old('condiciones') ? 'checked' : '' }}
                required
            >
            <p>Al crear la cuenta aceptas nuestros términos y condiciones.</p>

            <button type="submit" name="crear" class="btn-login" style="cursor:pointer;">Crear</button>
        </form>
    </div>
    </section>
@endsection

@push('scripts')
    <script>
        function snack(msg, redirect = null) {
            let bar = document.getElementById("snackbar");
            bar.innerHTML = msg;
            bar.classList.add("show");

            setTimeout(() => {
                bar.classList.remove("show");
                if (redirect) {
                    window.location.href = redirect;
                }
            }, 3500);
        }

        @if (session('status'))
            snack(@json(session('status')));
        @endif
    </script>
@endpush


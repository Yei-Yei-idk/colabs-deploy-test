@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <section class="auth-registrarse">
        <div class="formulario">
            <h1>Iniciar sesión</h1>

            {{-- Snackbar container --}}
            <div id="snackbar">
                @if (session('status'))
                    {{ session('status') }}
                @elseif (session('mensaje_login'))
                    {{ session('mensaje_login') }}
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
                <br><br>

                <button type="submit" name="login" class="btn-login">
                    Ingresar
                </button>
                <br>

                <p>
                    <a href="#">¿Has olvidado tu contraseña?</a>
                </p>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function snack(msg) {
            let bar = document.getElementById("snackbar");
            bar.innerHTML = msg;
            bar.classList.add("show");

            setTimeout(() => {
                bar.classList.remove("show");
            }, 3500);
        }

        if (session('status'))
            snack(json(session('status')));
        
        if (session('mensaje_login'))
            snack(json(session('mensaje_login')));
    </script>
@endpush


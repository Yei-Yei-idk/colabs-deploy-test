{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Colabs')</title>

    <link rel="icon" href="{{ asset('ASSETS/logo.png') }}" type="image/png">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    @stack('styles')
</head>
<body data-session-status="{{ session('status') ?? '' }}">

    {{-- ===== HEADER ===== --}}
    <header>
        <nav>
            {{-- Izquierda: logo + menú --}}
            <div class="nav-left">
                <a href="{{ route('inicio') }}" class="logo">
                    <img src="{{ asset('ASSETS/logo.png') }}" alt="Colabs">
                </a>
            </div>

            {{-- Menú centrado --}}
            <ul class="menu">
                <li><a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('nosotros') }}" class="{{ request()->routeIs('nosotros') ? 'active' : '' }}">Nosotros</a></li>
                <li><a href="{{ route('ubicacion') }}" class="{{ request()->routeIs('ubicacion') ? 'active' : '' }}">Encuéntranos</a></li>
                <li><a href="{{ route('servicios') }}" class="{{ request()->routeIs('servicios') ? 'active' : '' }}">Servicios</a></li>
            </ul>
 
            {{-- Derecha: botones de sesión --}}
            <div class="nav-right">
                <div class="botones-sesion">
                    @guest
                        <a href="{{ route('registrarse.mostrar') }}" class="btn-sesion registrarse">Registrarse</a>
                        <a href="{{ route('login') }}" class="btn-sesion iniciar">Iniciar sesión</a>
                    @else
                        @php
                            $isUnverified = !auth()->user()->hasVerifiedEmail();
                        @endphp

                        @if(in_array(auth()->user()->rol_id, [1, 2]))
                            <a href="{{ $isUnverified ? 'javascript:void(0)' : route('admin.dashboard') }}" 
                               class="btn-sesion iniciar" 
                               @if($isUnverified) onclick="snack('🔒 Primero completa el proceso de verificación.')" @endif>
                                Ir a tu panel
                            </a>
                        @else
                            <a href="{{ $isUnverified ? 'javascript:void(0)' : route('cliente.index') }}" 
                               class="btn-sesion iniciar"
                               @if($isUnverified) onclick="snack('🔒 Primero completa el proceso de verificación.')" @endif>
                                Ir a tu panel
                            </a>
                        @endif
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    {{-- ===== CONTENIDO ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer>
        <p>&copy; {{ date('Y') }} Colabs. Todos los derechos reservados.</p>
    </footer>

    {{-- Snackbar Global --}}
    <div id="snackbar"></div>

    <script src="{{ asset('js/global.js') }}"></script>

    {{-- Menú hamburguesa --}}
    <script src="{{ asset('js/menu_hamburguesa.js') }}"></script>

    @stack('scripts')
</body>
</html>
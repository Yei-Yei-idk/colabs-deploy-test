{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Colabs')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    @stack('styles')
</head>
<body>

    {{-- ===== HEADER ===== --}}
  <header>
        <nav>
            {{-- Izquierda: logo + menú --}}
            <div class="nav-left">
                <a href="{{ route('inicio') }}" class="logo">
                    <img src="{{ asset('ASSETS/logo.png') }}" alt="Colabs">
                </a>
                <ul class="menu">
                    <li><a href="{{ route('inicio') }}"
                           class="{{ request()->routeIs('inicio') ? 'active' : '' }}">Inicio</a></li>
                    <li><a href="{{ route('nosotros') }}" class="{{ request()->routeIs('nosotros') ? 'active' : '' }}">Nosotros</a></li>
                    <li><a href="{{ route('ubicacion') }}" class="{{ request()->routeIs('ubicacion') ? 'active' : '' }}">Encuéntranos</a></li>
                    <li><a href="{{ route('servicios') }}" class="{{ request()->routeIs('servicios') ? 'active' : '' }}">Servicios</a></li>
                </ul>
            </div>
 
            {{-- Derecha: botones de sesión --}}
            <div class="nav-right">
                <div class="botones-sesion">
                    @guest
                        <a href="{{ route('registrarse.mostrar') }}" class="btn-sesion registrarse">Registrarse</a>
                        <a href="{{ route('login') }}" class="btn-sesion iniciar">Iniciar sesión</a>
                    @else
                        <a href="{{ route('cliente.index') }}" class="btn-sesion iniciar">Ir a tu panel</a>
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

    <script>
        function snack(msg) {
            let bar = document.getElementById("snackbar");
            if (!bar) return;
            bar.innerHTML = msg;
            bar.classList.add("show");
            setTimeout(() => {
                bar.classList.remove("show");
            }, 3500);
        }

        @if (session('status'))
            snack("{{ session('status') }}");
        @endif
    </script>

    @stack('scripts')
</body>
</html>
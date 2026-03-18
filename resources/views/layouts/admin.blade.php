<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin') - Co•labs</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    {{-- CSS del panel admin. Mueve super.css a public/css/admin/admin.css --}}
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">

    @yield('styles')
</head>
<body>

{{-- ===================== SIDEBAR ===================== --}}
<aside class="sidebar">
    <div class="logo">
        <img src="{{ asset('ASSETS/logo.png') }}" alt="Logo Colabs">
        <label>Co•labs</label>
    </div>

    <nav>
        <ul>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('admin.espacios*') ? 'active' : '' }}">
                <a href="{{ route('admin.espacios.index') }}">Espacios</a>
            </li>
            <li class="{{ request()->routeIs('admin.reservas.index') ? 'active' : '' }}">
                <a href="{{ route('admin.reservas.index') }}">Reservas</a>
            </li>

            {{-- Submenú de solicitudes --}}
            <li>
                <details {{ request()->routeIs('admin.reservas.pendientes', 'admin.reservas.finalizadas') ? 'open' : '' }}>
                    <summary>Solicitudes de reservas</summary>
                    <ul>
                        <li class="{{ request()->routeIs('admin.reservas.pendientes') ? 'active' : '' }}">
                            <a href="{{ route('admin.reservas.pendientes') }}">Pendientes</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.reservas.finalizadas') ? 'active' : '' }}">
                            <a href="{{ route('admin.reservas.finalizadas') }}">Finalizadas</a>
                        </li>
                    </ul>
                </details>
            </li>

            <li class="{{ request()->routeIs('admin.copia_seguridad.menu') ? 'active' : '' }}">
                <a href="{{ route('admin.copia_seguridad.menu') }}">Copias de seguridad</a>
            </li>

            {{-- Solo visible para SuperAdmin (rol_id = 1) --}}
            @if(auth()->user()->rol_id == 1)
                <li class="{{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                    <a href="{{ route('admin.usuarios.index') }}">Gestión de usuarios</a>
                </li>
            @endif
        </ul>
    </nav>
</aside>

{{-- ===================== CONTENEDOR PRINCIPAL ===================== --}}
<div class="main-wrapper">

    {{-- HEADER --}}
    <header class="header">
        <div class="left">@yield('page-title', 'Dashboard')</div>
        <div class="right">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
            <button class="new-btn" onclick="document.getElementById('logout-form').submit()">
                Cerrar Sesión
            </button>
        </div>
    </header>

    {{-- CONTENIDO DE CADA PÁGINA --}}
    <main class="content">
        @yield('content')
    </main>

</div>{{-- /.main-wrapper --}}

@yield('scripts')
</body>
</html>

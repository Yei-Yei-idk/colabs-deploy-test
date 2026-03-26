<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin') - Co•labs</title>

    <link rel="icon" href="{{ asset('ASSETS/logo.png') }}" type="image/png">

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
            <button class="new-btn" onclick="document.getElementById('logout-modal').style.display='flex'">
                Cerrar Sesión
            </button>
        </div>
    </header>

    {{-- CONTENIDO DE CADA PÁGINA --}}
    <main class="content">
        @yield('content')
    </main>

</div>{{-- /.main-wrapper --}}

{{-- ===== MODAL CONFIRMAR CIERRE DE SESIÓN ===== --}}
<div id="logout-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:2rem 2.5rem; max-width:380px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18); text-align:center; animation:fadeInUp .2s ease;">
        <div style="font-size:2.5rem; margin-bottom:.5rem;">🔒</div>
        <h3 style="margin:0 0 .5rem; font-size:1.2rem; color:#1a1a2e;">¿Cerrar sesión?</h3>
        <p style="margin:0 0 1.5rem; color:#6b7280; font-size:.95rem;">¿Estás seguro de que deseas cerrar tu sesión como administrador?</p>
        <div style="display:flex; gap:.75rem; justify-content:center;">
            <button
                onclick="document.getElementById('logout-modal').style.display='none'"
                style="padding:.6rem 1.4rem; border-radius:8px; border:none; background:#9ca3af; color:#fff; font-size:.95rem; cursor:pointer; font-weight:600; transition:background .2s;"
                onmouseover="this.style.background='#6b7280'" onmouseout="this.style.background='#9ca3af'">
                Cancelar
            </button>
            <button
                onclick="document.getElementById('logout-form').submit()"
                style="padding:.6rem 1.4rem; border-radius:8px; border:none; background:#ef4444; color:#fff; font-size:.95rem; cursor:pointer; font-weight:600; transition:background .2s;"
                onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#ef4444'">
                Sí, cerrar sesión
            </button>
        </div>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
#logout-modal { backdrop-filter: blur(3px); }
</style>

@yield('scripts')
</body>
</html>

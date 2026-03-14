<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Colabs')</title>
    
    <!-- Fuente Inter desde Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/cliente/cliente.css') }}">
</head>
<body>

<header>
  <nav>
    <div class="nav-left">
      <!-- Se usa asset() para buscar en la carpeta "public" de Laravel -->
      <img src="{{ asset('ASSETS/logo.png') }}" alt="logo" loading="lazy">
      <span class="logo">Colabs</span>
    </div>
    
    <ul class="menu">
      <!-- Usamos request()->routeIs() para iluminar el link actual -->
      <li><a href="{{ route('cliente.index') }}" class="{{ request()->routeIs('cliente.index') ? 'active' : '' }}">Inicio</a></li>
      <li><a href="{{ route('cliente.buscar_espacios') }}" class="{{ request()->routeIs('cliente.buscar_espacios') ? 'active' : '' }}">Buscar espacios</a></li>
      <li><a href="{{ route('cliente.mis_reservas') }}" class="{{ request()->routeIs('cliente.mis_reservas') ? 'active' : '' }}">Mis reservas</a></li>
    </ul>

    <div class="nav-right">
      <!-- Botón de notificaciones -->
      <div class="notificaciones-dropdown">
        <button class="notificaciones-btn" id="btnNotificaciones">🔔</button>
        <div class="notificaciones-menu" id="notificacionesMenu">
          
          @forelse($notificaciones ?? [] as $reserva)
              <div class="notificacion {{ $reserva->rsva_estado }}">
                  @if ($reserva->rsva_estado == 'Aceptada')
                      <strong>¡Reserva confirmada!</strong>
                  @elseif ($reserva->rsva_estado == 'Rechazada')
                      <strong>Reserva rechazada</strong>
                  @else
                      <strong>Actualización de reserva</strong>
                  @endif

                  <!-- Suponiendo que haces relaciones con Eloquent (Ej: $reserva->espacio->esp_nombre) -->
                  <p>Espacio: {{ $reserva->espacio->esp_nombre ?? 'N/D' }}</p>
                  <p>Fecha: {{ \Carbon\Carbon::parse($reserva->rsva_fecha)->format('d M') }} de {{ \Carbon\Carbon::parse($reserva->rsva_hora_inicio)->format('g:i A') }} a {{ \Carbon\Carbon::parse($reserva->rsva_hora_fin)->format('g:i A') }}</p>

                  @if ($reserva->rsva_estado == 'Aceptada')
                      <button class='btn-ver' onclick="window.location.href='{{ route('cliente.mis_reservas') }}'">Ver detalles</button>
                  @elseif ($reserva->rsva_estado == 'Rechazada')
                      <button class='btn-alternativa' onclick="window.location.href='{{ route('cliente.buscar_espacios') }}'">Buscar alternativa</button>
                  @else
                      <button class='btn-reseña' onclick="window.location.href='{{ route('cliente.mis_reservas') }}'">Ver reserva</button>
                  @endif
              </div>
          @empty
              <p style='text-align:center; color:#555;'>No tienes notificaciones recientes.</p>
          @endforelse

        </div>
      </div>

      <!-- Perfil -->
      <div class="perfil-dropdown">
        <button class="perfil-btn" onclick="document.getElementById('menuPerfil').classList.toggle('show')">
          <img src="{{ asset('ASSETS/icon.webp') }}" alt="avatar" height="200px">
          <!-- Obteniendo datos del usuario autenticado en Laravel -->
          <span>{{ auth()->user()->nombre ?? 'Usuario' }}</span>
        </button>
        <ul class="dropdown-menu" name="menuPerfil" id="menuPerfil" hidden>
          <li><a href="{{ route('cliente.perfil') ?? '#' }}">Mi perfil</a></li>
          <li><a href="{{ route('cliente.mis_reservas') ?? '#' }}">Mis reservas</a></li>
          <li><a href="#">Ayuda y soporte</a></li>
          <li class="cerrar"><a style="cursor: pointer;" onclick="openLogoutPopup()">Cerrar sesión</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<!-- POPUP DE CERRAR SESIÓN -->
<div id="logoutPopup" class="popup-overlay" style="display: none;">
  <div class="popup">
    <div class="popup-icon">
      <img src="{{ asset('ASSETS/logout.svg') }}" alt="logout icon">
    </div>
    <h3>Cerrar sesión</h3>
    <p>¿Estás seguro de que quieres cerrar tu sesión?</p>
    <div class="popup-buttons">
      <button class="cancelar-btn" onclick="closeLogoutPopup()">Cancelar</button>
      
      <!-- En Laravel el logout debe ser por POST por seguridad. Esto lo envía -->
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
      </form>
      <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</button>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- AQUÍ SE INYECTARÁ EL CONTENIDO DE LAS PÁGINAS -->
<!-- ========================================== -->
<main>
    @yield('content')
</main>

<script>
  const btnNotificaciones = document.getElementById('btnNotificaciones');
  const menuNotificaciones = document.getElementById('notificacionesMenu');

  if(btnNotificaciones) {
      btnNotificaciones.addEventListener('click', (e) => {
        e.stopPropagation();
        menuNotificaciones.style.display = menuNotificaciones.style.display === 'flex' ? 'none' : 'flex';
      });
  }

  document.addEventListener('click', () => {
    if(menuNotificaciones) menuNotificaciones.style.display = 'none';
  });

  if(menuNotificaciones) {
      menuNotificaciones.addEventListener('click', (e) => {
        e.stopPropagation();
      });
  }

  /* ==== POPUP LOGOUT ==== */
  function openLogoutPopup() {
    document.getElementById("logoutPopup").style.display = "flex";
    const menuPerfil = document.getElementById("menuPerfil");
    if(menuPerfil) {
        menuPerfil.classList.remove("show");
        menuPerfil.hidden = true;
    }
  }

  function closeLogoutPopup() {
    document.getElementById("logoutPopup").style.display = "none";
  }

  document.getElementById("logoutPopup").addEventListener("click", function(e) {
    if (e.target === this) {
      closeLogoutPopup();
    }
  });

  // Nota: Ya no necesitamos el JS que lee la URL y marca el menú manual porque 
  // Blade ahora lo hace al cargar usando el helper `request()->routeIs()`
</script>

<!-- Espacio para que las páginas inserten sus propios scripts -->
@yield('scripts')

</body>
</html>

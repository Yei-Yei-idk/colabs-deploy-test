<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Colabs')</title>
    
    <link rel="icon" href="{{ asset('ASSETS/logo.png') }}" type="image/png">
    
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
      <li><a href="{{ route('cliente.buscar_espacios') }}" class="{{ request()->routeIs('cliente.buscar_espacios', 'cliente.reservar') ? 'active' : '' }}">Buscar espacios</a></li>
      <li><a href="{{ route('cliente.mis_reservas') }}" class="{{ request()->routeIs('cliente.mis_reservas', 'cliente.detalles_reserva') ? 'active' : '' }}">Mis reservas</a></li>
    </ul>

    <div class="nav-right">
      <!-- Botón de notificaciones -->
      <div class="notificaciones-dropdown">
        <button class="notificaciones-btn" id="btnNotificaciones" style="position: relative;">
            🔔
            <span id="notif-indicator" style="display:none; position:absolute; top:2px; right:2px; width:8px; height:8px; background-color:red; border-radius:50%; border:1px solid white;"></span>
        </button>
        <div class="notificaciones-menu" id="notificacionesMenu">
          
          @php
              $notificaciones = auth()->check() ? \App\Models\Reserva::with('espacio')
                  ->where('user_id', auth()->id())
                  ->orderBy('reserva_id', 'desc')
                  ->take(5)
                  ->get() : [];
              $notificacionesStr = implode('|', collect($notificaciones)->map(function($r) { return $r->reserva_id . '-' . $r->rsva_estado; })->toArray());
          @endphp
          @forelse($notificaciones as $reserva)
              @php /** @var \App\Models\Reserva $reserva */ @endphp
              <div class="notificacion {{ $reserva->rsva_estado }}">
                  @if ($reserva->rsva_estado == 'Aceptada')
                      <strong>✅ ¡Reserva confirmada!</strong>
                  @elseif ($reserva->rsva_estado == 'Rechazada' || $reserva->rsva_estado == 'Cancelada')
                      <strong class="alert-error">❌ Reserva {{ strtolower($reserva->rsva_estado) }}</strong>
                  @elseif ($reserva->rsva_estado == 'Pendiente')
                      <strong class="alert-warning">⏳ Reserva en proceso</strong>
                  @elseif ($reserva->rsva_estado == 'Finalizada')
                      <strong>🌟 Reserva completada</strong>
                  @endif

                  <!-- Suponiendo que haces relaciones con Eloquent (Ej: $reserva->espacio->esp_nombre) -->
                  <p>Espacio: {{ $reserva->espacio->esp_nombre ?? 'N/D' }}</p>
                  <p class="notification-time">{{ \Carbon\Carbon::parse($reserva->rsva_fecha)->format('d M') }} de {{ \Carbon\Carbon::parse($reserva->rsva_hora_inicio)->format('g:i A') }} a {{ \Carbon\Carbon::parse($reserva->rsva_hora_fin)->format('g:i A') }}</p>

                  @if ($reserva->rsva_estado == 'Aceptada' || $reserva->rsva_estado == 'Pendiente')
                      <button class='btn-ver btn-dark' data-url="{{ route('cliente.detalles_reserva', $reserva->reserva_id) }}" onclick="window.location.href=this.getAttribute('data-url')">Ver detalles</button>
                  @elseif ($reserva->rsva_estado == 'Rechazada' || $reserva->rsva_estado == 'Cancelada')
                      <button class='btn-alternativa' data-url="{{ route('cliente.buscar_espacios') }}" onclick="window.location.href=this.getAttribute('data-url')">Buscar alternativa</button>
                  @else
                      <button class='btn-reseña' data-url="{{ route('cliente.detalles_reserva', $reserva->reserva_id) }}" onclick="window.location.href=this.getAttribute('data-url')">Ver reserva</button>
                  @endif
              </div>
          @empty
              <p class="text-center text-muted">No tienes notificaciones recientes.</p>
          @endforelse

        </div>
      </div>

      <!-- Perfil -->
      <div class="perfil-dropdown">
        <button class="perfil-btn" onclick="document.getElementById('menuPerfil').classList.toggle('show')">
          <div class="review-avatar {{ auth()->user()->avatar_color }}" style="margin-right: 15px; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; color: white; font-weight: bold; font-size: 1.05rem; flex-shrink: 0;">{{ auth()->user()->avatar_initial }}</div>
          <!-- Obteniendo datos del usuario autenticado en Laravel -->
          <span>{{ auth()->user()->first_name }}</span>
        </button>
        <ul class="dropdown-menu" name="menuPerfil" id="menuPerfil" hidden>
          <li><a href="{{ route('cliente.perfil') }}">Mi perfil</a></li>
          <li><a href="{{ route('cliente.mis_reservas') }}">Mis reservas</a></li>
          <li><a href="#">Ayuda y soporte</a></li>
          <li class="cerrar"><a class="pointer" onclick="openLogoutPopup()">Cerrar sesión</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<!-- POPUP DE CERRAR SESIÓN -->
<div id="logoutPopup" class="popup-overlay" style="display: none;">
  <div class="popup">
    <div class="popup-icon">
      <img src="{{ asset('/ASSETS/logout.svg') }}" alt="logout icon">
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

<main>
    @yield('content')
</main>


<!-- MODAL DE CALIFICACIÓN -->
<div id="modalCalificar" class="modal-overlay modal-hidden">
  <div class="modal-container modal-small">
    <div class="modal-header">
      <h3>Califica tu experiencia</h3>
      <button class="modal-close" onclick="closeReviewModal()">&times;</button>
    </div>
    <div class="modal-content">
      <p class="modal-description">Tu opinión es muy valiosa para nosotros y para otros usuarios.</p>
      
      <form id="formCalificar" action="{{ route('cliente.calificar') }}" method="POST">
        @csrf
        <input type="hidden" name="espacio_id" id="modal_espacio_id">
        <input type="hidden" name="reserva_id" id="modal_reserva_id">
        
        <div class="form-group">
          <label class="form-label">¿Qué te pareció el espacio?</label>
          <div class="star-rating" id="modalStarRating">
            <span class="star" data-rating="1">★</span>
            <span class="star" data-rating="2">★</span>
            <span class="star" data-rating="3">★</span>
            <span class="star" data-rating="4">★</span>
            <span class="star" data-rating="5">★</span>
          </div>
          <div class="rating-text" id="modalRatingText">5 de 5 estrellas</div>
          <input type="hidden" name="calif_puntuacion" id="modal_puntuacion" value="5">
        </div>

        <div class="form-group mt-20">
          <label for="modal_calif_txt" class="form-label">Tu reseña</label>
          <textarea name="calif_txt" id="modal_calif_txt" class="form-textarea" placeholder="Describe tu experiencia..." maxlength="1800" required></textarea>
          <div class="char-count"><span id="modalCharCount">0</span> / 1800</div>
        </div>

        <button type="submit" class="btn-submit-review">Publicar reseña</button>
      </form>
    </div>
  </div>
</div>


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

        let sessionStatus = "{{ session('status') ?? session('success') ?? session('error') }}";
        if (sessionStatus) {
            snack(sessionStatus);
        }
  </script>

    <script src="{{ asset('js/cliente/global_cliente.js') }}"></script>
  
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnNotificaciones = document.getElementById('btnNotificaciones');
            const indicator = document.getElementById('notif-indicator');
            const currentState = "{{ $notificacionesStr ?? '' }}";
            const lastSeenState = localStorage.getItem('colabs_notifications_state');

            // Mostrar el indicador si el estado actual es diferente al guardado y hay notificaciones
            if (currentState && currentState !== lastSeenState) {
                if (indicator) indicator.style.display = 'block';
            }

            if (btnNotificaciones) {
                btnNotificaciones.addEventListener('click', () => {
                    // Al abrir el menú, ocultar indicador y guardar el estado actual como "visto"
                    if (indicator) indicator.style.display = 'none';
                    if (currentState) {
                        localStorage.setItem('colabs_notifications_state', currentState);
                    }
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>

@extends('layouts.cliente')

@section('title', $espacio->esp_nombre . ' - Reservar')

@section('content')
    @php
        $hoyBogota = \Carbon\Carbon::now('America/Bogota')->format('Y-m-d');
        $serverNowEpochMs = \Carbon\Carbon::now('America/Bogota')->getTimestampMs();
    @endphp
    <!-- SECCIÓN RESERVAR AHORA -->
    <div class="continer">
        <div class="reservar-container">
            <!-- Lado izquierdo -->
            <div class="left-section">
                <div class="galeria-img">
                    <!-- Imagen principal -->
                    <div class="main-image-container">
                        <div class="main-image">
                            <div class="image-placeholder">
                                @if(!empty($imagenes))
                                    <img id="mainImage" src="{{ asset('uploads/' . $imagenes[0]) }}"
                                        alt="{{ $espacio->esp_nombre }}"
                                        data-fallback="{{ asset('uploads/OF1 .jpeg') }}" onerror="this.src=this.getAttribute('data-fallback')">
                                @else
                                    <img src="{{ asset('uploads/OF1 .jpeg') }}" alt="Sin imagen">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="detalles-espacio">
                    <div class="office-title-section">
                        <h1>{{ $espacio->esp_nombre }}</h1>
                        <div class="rating-section">
                            <span class="rating-stars">★</span>
                            <span class="rating-number">{{ $promedio }}</span>
                            <span class="rating-count">({{ $num_resenas }} reseñas)</span>
                        </div>
                    </div>
                    <!-- Descripción -->
                    <div class="office-description">
                        <p>{!! nl2br(e($espacio->esp_descripcion)) !!}</p>
                    </div>
                </div>
                <!-- Reseñas -->
                <div class="detalles-espacio">
                    <div class="reviews-section">
                        <div class="reviews-header">
                            <h3>Reseñas de usuarios</h3>
                            @if($num_resenas > 0)
                                <a class="ver-todas" onclick="openReviewsModal(); return false;">Ver todas ({{ $num_resenas }})</a>
                            @endif
                        </div>
                        @if($num_resenas > 0)
                            @php
                                $reviews_to_show = array_slice($calificaciones, 0, 2);
                                $colors = ['purple', 'green', 'orange', 'blue'];
                            @endphp
                            @foreach($reviews_to_show as $index => $review)
                                @php
                                    $last_digit = (int) substr((string) ($review['user_id'] ?? $index), -1);
                                    $color = $colors[$last_digit % 4];
                                    $inicial = strtoupper(substr($review['user_nombre'], 0, 1));
                                @endphp
                                <div class="review-card">
                                    <div class="review-avatar {{ $color }}">{{ $inicial }}</div>
                                    <div class="review-content">
                                        <div class="reviewer-info">
                                            <span class="reviewer-name">{{ explode(' ', trim($review['user_nombre']))[0] }}</span>
                                            <span class="review-date">Recientemente</span>
                                        </div>
                                        <div class="review-stars">@for($i = 1; $i <= 5; $i++){{ $i <= $review['calif_puntuacion'] ? '★' : '☆' }}@endfor</div>
                                        <p class="review-text">{{ $review['calif_txt'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Aún no hay reseñas para este espacio.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar de reserva -->
            <div class="right-section">
                <div class="booking-sidebar">
                    <div class="price-display">
                        <span class="price-amount">${{ number_format($espacio->esp_precio_hora, 0, ',', '.') }} COP</span>
                        <span class="price-period">por hora</span>
                    </div>
                    <form id="bookingForm" class="booking-form">
                        @csrf
                        <input type="hidden" name="espacio_id" value="{{ $espacio->espacio_id }}">
                        <input type="hidden" name="precio_hora" value="{{ $espacio->esp_precio_hora }}">
                        <div class="form-group">
                            <label class="form-label">Selecciona la fecha</label>
                            <div class="date-input-container">
                                <input type="date" id="fecha" name="fecha" class="form-input"
                                    min="{{ $hoyBogota }}" value="{{ $hoyBogota }}" required>
                                <span class="calendar-icon">📅</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Horario de Reserva</label>
                            <div class="time-row">
                                <div class="time-field">
                                    <label class="time-label">Hora de inicio</label>
                                    <div class="time-input-container">
                                        <select id="hora_inicio" name="hora_inicio" class="time-select" required>
                                            <option value="">Seleccionar hora</option>
                                        </select>
                                        <span class="clock-icon">🕘</span>
                                    </div>
                                </div>
                                <div class="time-field">
                                    <label class="time-label">Hora de fin</label>
                                    <div class="time-input-container">
                                        <select id="hora_fin" name="hora_fin" class="time-select" required disabled>
                                            <option value="">Primero selecciona hora de inicio</option>
                                        </select>
                                        <span class="clock-icon">🕘</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="availability-box" id="availabilityBox">
                            <div id="availabilityContent">
                                <p class="text-center text-muted">
                                    Selecciona fecha y hora para verificar disponibilidad
                                </p>
                            </div>
                        </div>
                        <!-- Secciones ocultas hasta que haya disponibilidad confirmada -->
                        <div class="form-group" id="guestsSection" style="display:none;">
                            <div class="guests-header">
                                <label class="form-label">Número de invitados</label>
                                <span class="guests-icon">👥</span>
                            </div>
                            <div class="guests-selector">
                                <button type="button" class="guest-btn minus" onclick="changeGuests(-1)">-</button>
                                <span class="guest-count" id="guestCount">1 invitado</span>
                                <input type="hidden" id="num_invitados" name="num_invitados" value="1">
                                <button type="button" class="guest-btn plus" onclick="changeGuests(1)">+</button>
                            </div>
                            <small class="text-muted">Capacidad máxima: {{ $espacio->esp_capacidad }} personas</small>
                        </div>
                        <div class="form-group" id="descriptionSection" style="display:none;">
                            <label class="form-label">Descripción de la reserva (Obligatorio)</label>
                            <textarea name="descripcion" class="form-textarea" required
                                placeholder="Describe la actividad que realizarás en el espacio."></textarea>
                        </div>
                        <div class="pricing-summary" id="pricingSummary" style="display:none;">
                            <div class="pricing-line">Elija el horario de la reserva.</div>
                        </div>
                        <button type="submit" class="reserve-button" id="reserveBtn" disabled style="opacity:0.5;cursor:not-allowed;">
                            Verificar disponibilidad
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Reseñas -->
    <div id="reviewsModal" class="modal-overlay modal-hidden">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Reseñas de usuarios</h3>
                <button class="modal-close" onclick="closeReviewsModal()">&times;</button>
            </div>
            <div class="modal-content">
                @if($num_resenas > 0)
                    @php $colors = ['purple', 'green', 'orange', 'blue']; @endphp
                    @foreach($calificaciones as $index => $review)
                        @php
                            $last_digit = (int) substr((string) ($review['user_id'] ?? $index), -1);
                            $color = $colors[$last_digit % 4];
                            $inicial = strtoupper(substr($review['user_nombre'], 0, 1));
                        @endphp
                        <div class="modal-review">
                            <div class="modal-review-avatar {{ $color }}">{{ $inicial }}</div>
                            <div class="modal-review-content">
                                <div class="modal-reviewer-info">
                                    <span class="modal-reviewer-name">{{ explode(' ', trim($review['user_nombre']))[0] }}</span>
                                </div>
                                <div class="modal-review-stars">@for($i = 1; $i <= 5; $i++){{ $i <= $review['calif_puntuacion'] ? '★' : '☆' }}@endfor</div>
                                <p class="modal-review-text">{{ $review['calif_txt'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center p-20">No hay reseñas disponibles.</p>
                @endif
            </div>
            <div class="modal-footer">
                <span class="modal-total-reviews">Mostrando {{ $num_resenas }} reseña(s)</span>
            </div>
        </div>
    </div>

    <!-- POPUP DE CONFIRMAR RESERVA -->
    <div id="confirmBookingPopup" class="popup-overlay modal-hidden">
        <div class="booking-popup">
            <div class="booking-popup-header">
                <div class="booking-popup-icon">📋</div>
                <h3>Confirmar reserva de espacio</h3>
                <p class="booking-popup-subtitle">Revisa los detalles antes de confirmar tu reserva</p>
            </div>
            <div class="booking-popup-content">
                <div class="booking-section">
                    <h4>Resumen de la reserva</h4>
                    <div class="booking-detail"><span class="booking-label">Espacio:</span><span class="booking-value" id="confirmEspacio"></span></div>
                    <div class="booking-detail"><span class="booking-label">Fecha:</span><span class="booking-value" id="confirmFecha"></span></div>
                    <div class="booking-detail"><span class="booking-label">Horario:</span><span class="booking-value" id="confirmHorario"></span></div>
                    <div class="booking-detail"><span class="booking-label">Duración:</span><span class="booking-value" id="confirmDuracion"></span></div>
                    <div class="booking-detail"><span class="booking-label">Capacidad:</span><span class="booking-value" id="confirmCapacidad"></span></div>
                    <div class="booking-detail"><span class="booking-label">Invitados:</span><span class="booking-value" id="confirmInvitados"></span></div>
                </div>
                <div class="booking-services">
                    <h4>✅ Servicios incluidos</h4>
                    <ul class="services-list">
                        <li>📶 WiFi de alta velocidad</li>
                        <li>☕ Café y refrigerios</li>
                        <li>🔒 Seguridad 24/7</li>
                    </ul>
                </div>
                <div class="booking-important">
                    <h4>⚠️ Importante</h4>
                    <p>Recibirás un email de confirmación si el espacio es aprobado. La reserva puede ser rechazada o confirmada según disponibilidad.</p>
                </div>
                <div class="booking-total">
                    <div class="booking-detail"><span class="booking-label" id="confirmPrecio"></span></div>
                    <div class="booking-detail total-line">
                        <span class="booking-label-total">Total</span>
                        <span class="booking-value-total" id="confirmTotal"></span>
                    </div>
                </div>
            </div>
            <div class="booking-popup-buttons">
                <button class="booking-cancel-btn" onclick="closeConfirmBookingPopup()">Cancelar</button>
                <button class="booking-confirm-btn" onclick="confirmBooking()">Confirmar reserva</button>
            </div>
        </div>
    </div>


    <!-- Modal de Alternativas -->
    <div id="alternativasModal" class="modal-overlay modal-hidden">
        <div class="modal-container alt-modal-container">
            <div class="modal-header alt-modal-header">
                <h3 class="alt-modal-title">
                    <span class="alt-modal-title-icon"></span> Espacios Similares Libres
                </h3>
                <button class="alt-modal-close" onclick="closeAlternativasModal()">&times;</button>
            </div>
            <div class="modal-content alt-modal-body" id="alternativasModalBody">
                <!-- Contenido dinámico desde JS -->
            </div>
        </div>
    </div>

    <div id="reservaConfigData" 
         data-precio-hora="{{ $espacio->esp_precio_hora }}"
         data-capacidad="{{ $espacio->esp_capacidad }}"
         data-espacio-id="{{ $espacio->espacio_id }}"
         data-espacio-nombre="{{ $espacio->esp_nombre }}"
         data-csrf="{{ csrf_token() }}"
         data-verificar-url="{{ route('cliente.verificar_disponibilidad') }}"
         data-alternativas-url="{{ route('cliente.alternativas') }}"
         data-confirmar-url="{{ route('cliente.confirmar_reserva') }}"
         data-server-now-epoch-ms="{{ $serverNowEpochMs }}"
         data-server-timezone="America/Bogota"
         style="display:none;"></div>
    <script>
        const configElement = document.getElementById('reservaConfigData');
        window.reservaConfig = {
            precioHora: parseFloat(configElement.getAttribute('data-precio-hora')),
            capacidadMaxima: parseInt(configElement.getAttribute('data-capacidad'), 10),
            espacioId: parseInt(configElement.getAttribute('data-espacio-id'), 10),
            espacioNombre: configElement.getAttribute('data-espacio-nombre'),
            csrfToken: configElement.getAttribute('data-csrf'),
            verificarUrl: configElement.getAttribute('data-verificar-url'),
            alternativasUrl: configElement.getAttribute('data-alternativas-url'),
            confirmarUrl: configElement.getAttribute('data-confirmar-url'),
            serverNowEpochMs: parseInt(configElement.getAttribute('data-server-now-epoch-ms'), 10),
            serverTimezone: configElement.getAttribute('data-server-timezone') || 'America/Bogota'
        };
    </script>
    <script src="{{ asset('js/cliente/reserva.js?v=' . time()) }}"></script>
@endsection

@extends('layouts.cliente')

@section('title', 'Detalles de Reserva - COLABS')

@section('content')
<div class="detalles-container mt-20">
    @if(session('error'))
        <div class="alert alert-error w-full">
            {{ session('error') }}
        </div>
    @endif

    @php
        $fecha_formato = \Carbon\Carbon::parse($reserva->fecha)->translatedFormat('d \d\e F, Y');
        $horaInicio = \Carbon\Carbon::parse($reserva->hora_inicio);
        $horaFin = \Carbon\Carbon::parse($reserva->hora_fin);
        $diferencia_horas = $horaFin->diffInHours($horaInicio);
        $hora_inicio_formato = $horaInicio->format('h:i A');
        $hora_fin_formato = $horaFin->format('h:i A');
        $total_estimado = $diferencia_horas * $reserva->esp_precio_hora;
    @endphp

    <div class="gallery-section">
        <div class="main-image">
            @if($imgSrc)
                <img src="{{ asset('uploads/' . $imgSrc) }}" alt="{{ $reserva->esp_nombre }}"
                     onerror="this.src='{{ asset('uploads/OF1 .jpeg') }}'">
            @else
                <img src="{{ asset('uploads/OF1 .jpeg') }}" alt="Espacio reservado">
            @endif
        </div>

    </div>

    <div class="booking-details">
            <div class="details-card">
                <h2 class="room-title">{{ $reserva->esp_nombre }}</h2>
                <p>{{ $reserva->esp_descripcion }}</p>

                <div class="booking-info">
                    <div class="info-row">
                        <span class="info-label">Fecha:</span>
                        <span class="info-value" style="text-transform: capitalize;">{{ $fecha_formato }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Horario:</span>
                        <span class="info-value">{{ $hora_inicio_formato }} - {{ $hora_fin_formato }} ({{ $diferencia_horas }}h)</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Capacidad:</span>
                        <span class="info-value">{{ $reserva->esp_capacidad }} personas</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo:</span>
                        <span class="info-value">{{ $reserva->esp_tipo }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <span class="info-value">{{ $reserva->estado }}</span>
                    </div>
                </div>

                <div class="cost-summary">
                    <div class="cost-row">
                        <span>${{ number_format($reserva->esp_precio_hora, 0, ',', '.') }} x hora</span>
                    </div>
                    <div class="total-row">
                        <span>Total estimado:</span>
                        <span>${{ number_format($total_estimado, 0, ',', '.') }} COP</span>
                    </div>
                </div>

                @if($reserva->estado === 'Pendiente')
                    <form action="{{ route('cliente.cancelar_reserva') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas cancelar esta reserva pendiente?');">
                        @csrf
                        <input type="hidden" name="reserva_id" value="{{ $reserva->reserva_id }}">
                        <button type="submit" class="cancel-btn">
                            Cancelar reserva
                        </button>
                    </form>

                @elseif($reserva->estado === 'Aceptada')
                    <p style="color:orange; font-weight:bold;">Esta reserva ya fue aceptada. No puedes cancelarla.</p>

                @elseif($reserva->estado === 'Finalizada')
                    <div class="reserva-finalizada-info">
                        <p>✓ Esta reserva ha sido completada</p>
                    </div>
                    
                    @if(!$tiene_calificacion)
                        <button type="button" onclick="openReviewModal({{ $reserva->espacio_id }}, {{ $reserva->reserva_id }})" 
                           class="btn-resena btn-calificar mt-15 w-full-btn" style="padding: 12px; border-radius:8px; font-weight:bold; cursor:pointer;">
                            ⭐ Califica tu experiencia
                        </button>
                    @else
                        <div class="ya-calificado success-text">
                            ✓ Ya calificaste este espacio
                        </div>
                    @endif

                @elseif($reserva->estado === 'Cancelada')
                    <p style="color:red; font-weight:bold;">Esta reserva ya fue cancelada.</p>

                @else
                    <p style="color:gray;">Esta reserva no puede cancelarse (estado: {{ $reserva->estado }}).</p>
                @endif
            </div>
    </div>
</div>
@endsection

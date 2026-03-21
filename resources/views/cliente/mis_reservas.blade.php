@extends('layouts.cliente')

@section('title', 'Mis Reservas - COLABS')

@section('content')
<div class="mis-reservas-header">
    <h2>Mis Reservas</h2>
    <p class="text-muted">Gestiona todas tus reservas, su estado, y su historial.</p>
</div>

@if(session('success'))
    <div class="alert alert-success mis-reservas-list">
        {{ session('success') }}
    </div>
@endif

<div class="espacios-listado mis-reservas-list">
    @forelse($reservas as $reserva)
        @php
            // Obtener imagen del espacio
            $imagen = \App\Models\Imagen::where('espacio_id', $reserva->espacio_id)->first();
            $imgSrc = $imagen ? $imagen->foto : null;
            
            // Calcular info dinámica (Horas y Costo)
            $horaInicio = \Carbon\Carbon::parse($reserva->hora_inicio);
            $horaFin = \Carbon\Carbon::parse($reserva->hora_fin);
            $diferencia_horas = $horaFin->diffInHours($horaInicio);
            $total_estimado = $diferencia_horas * $reserva->esp_precio_hora;
            $fecha_formato = \Carbon\Carbon::parse($reserva->fecha)->translatedFormat('d \d\e F, Y');
        @endphp
        
        <div class="reserva-card-main {{ 'border-' . strtolower($reserva->estado) }}">
            <div class="reserva-id-tag">
                #{{ str_pad($reserva->reserva_id, 4, '0', STR_PAD_LEFT) }}
            </div>

            @if($imgSrc)
                <img src="{{ asset('uploads/' . $imgSrc) }}" alt="{{ $reserva->esp_nombre }}"
                     data-fallback="{{ asset('uploads/OF1 .jpeg') }}" onerror="this.src=this.getAttribute('data-fallback')">
            @else
                <img src="{{ asset('uploads/OF1 .jpeg') }}" alt="Sin imagen">
            @endif
            
            <div class="reserva-info-body">
                <h3>{{ $reserva->esp_nombre }}</h3>
                <p>{{ Str::limit($reserva->esp_descripcion, 80) }}</p>
                
                <div class="reserva-meta">
                    <div>📅 <strong>{{ $fecha_formato }}</strong></div>
                    <div>🕒 <strong>{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('h:i A') }}</strong> ({{ $diferencia_horas }}h)</div>
                </div>
            </div>

            <div class="reserva-actions-column">
                <div class="reserva-total-box">
                    <div class="reserva-total-label">Total:</div>
                    <div class="reserva-total-amount">${{ number_format($total_estimado, 0, ',', '.') }}</div>
                </div>

                <div class="reserva-badge {{ 'badge-' . strtolower($reserva->estado) }}">
                    {{ strtoupper($reserva->estado) }}
                </div>

                @if($reserva->estado === 'Finalizada')
                    @php
                        $ya_calificado = \App\Models\Calificacion::where('reserva_id', $reserva->reserva_id)->exists();
                    @endphp
                    
                    @if(!$ya_calificado)
                        <button type="button" data-espacio-id="{{ $reserva->espacio_id }}" data-reserva-id="{{ $reserva->reserva_id }}" onclick="openReviewModal(this.getAttribute('data-espacio-id'), this.getAttribute('data-reserva-id'))" 
                                class="btn-reservar btn-calificar">
                            ⭐ Calificar
                        </button>
                    @endif
                @endif

                <a href="{{ route('cliente.detalles_reserva', $reserva->reserva_id) }}" class="btn-reservar mt-5">
                    Ver Detalles →
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h3>¡Aún no tienes reservas!</h3>
            <p class="text-muted mb-20">Explora nuestros espacios y encuentra el lugar ideal para trabajar.</p>
            <a href="{{ route('cliente.buscar_espacios') }}" class="btn-principal">Buscar Espacios</a>
        </div>
    @endforelse
</div>
@endsection

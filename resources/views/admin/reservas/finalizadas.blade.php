@extends('layouts.admin')

@section('title', 'Reservas Finalizadas')
@section('page-title', 'Solicitudes de reservas')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/reservas.css') }}">
@endsection

@section('content')

<div class="page-intro">
    <h1>Reservas finalizadas</h1>
    <p>Visualiza las reservas finalizadas en una fecha seleccionada.</p>
</div>

<!-- NAVEGACIÓN Y FILTRO DE FECHAS -->
<nav class="navegacion-fechas">
    <a href="{{ route('admin.reservas.finalizadas', ['fecha' => $fechaAnterior]) }}" class="btn-nav">
        ⬅ Día anterior
    </a>

    <form method="GET" action="{{ route('admin.reservas.finalizadas') }}">
        <input type="date" class="input-fecha" name="fecha" value="{{ $fecha->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()">
    </form>

    @if ($fecha->lt(\Carbon\Carbon::today()))
        <a href="{{ route('admin.reservas.finalizadas', ['fecha' => $fechaSiguiente]) }}" class="btn-nav">
            Día siguiente ➡
        </a>
    @endif
</nav>

<div class="reservas-grid">
    @forelse ($reservas as $r)
        <article class="reserva-card">
            <div class="reserva-header">
                <span class="reserva-id">#{{ $r->reserva_id }}</span>
                <span class="reserva-badge badge-finalizada">{{ $r->rsva_estado }}</span>
            </div>

            <div class="user-info">
                <h2 class="user-name">{{ $r->usuario->user_nombre ?? 'Usuario Desconocido' }}</h2>
                <p class="user-email">{{ $r->usuario->user_correo ?? 'Sin Correo' }}</p>
            </div>

            <div class="space-info">
                <div class="space-name">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    {{ $r->espacio->esp_nombre ?? 'Espacio No Encontrado' }}
                </div>
                <div class="space-capacity">
                    Capacidad: {{ $r->espacio->esp_capacidad ?? '?' }} Persona(s)
                </div>
            </div>

            @if($r->rsva_descripcion)
                <div class="reserva-description">
                    <span class="description-label">Descripción del cliente:</span>
                    {{ $r->rsva_descripcion }}
                </div>
            @endif
            
            <div class="reserva-details">
                <div class="detail-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    {{ ucfirst(\Carbon\Carbon::parse($r->rsva_fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY')) }}
                </div>
                <div class="detail-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    💲 {{ number_format($r->espacio->esp_precio_hora ?? 0, 0, ',', '.') }} COP
                </div>
            </div>

            {{-- Aquí podrías añadir acciones adicionales para finalizadas si se requiere, como "Ver Calificación" --}}
        </article>
    @empty
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <h3>No hay reservas finalizadas para esta fecha</h3>
            <p>Intenta seleccionar otro día o consulta las reservas de hoy.</p>
        </div>
    @endforelse
</div>

@endsection

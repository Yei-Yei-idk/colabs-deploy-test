@extends('layouts.admin')

@section('title', 'Solicitudes Pendientes')
@section('page-title', 'Solicitudes de reservas')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/reservas.css') }}">
@endsection

@section('content')

@if(session('success'))
    <div class="alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
    </div>
@endif

<div class="page-intro">
    <h1>Solicitudes de reservas</h1>
    <p>Confirma las solicitudes de los clientes para asegurar su espacio.</p>
</div>

<div class="reservas-grid">
    @forelse ($reservas as $r)
        <article class="reserva-card">
            <div class="reserva-header">
                <span class="reserva-id">#{{ $r->reserva_id }}</span>
                <span class="reserva-badge badge-pendiente">{{ $r->rsva_estado }}</span>
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
                    {{ number_format($r->espacio->esp_precio_hora ?? 0, 0, ',', '.') }} COP / hora
                </div>
            </div>

            <footer class="reserva-footer">
                <form action="{{ route('admin.reservas.actualizar_estado') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reserva_id" value="{{ $r->reserva_id }}">
                    <input type="hidden" name="nuevo_estado" value="Rechazada">
                    <button type="submit" class="btn-action btn-rechazar">Rechazar</button>
                </form>

                <form action="{{ route('admin.reservas.actualizar_estado') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reserva_id" value="{{ $r->reserva_id }}">
                    <input type="hidden" name="nuevo_estado" value="Aceptada">
                    <button type="submit" class="btn-action btn-aceptar">Aceptar</button>
                </form>
            </footer>
        </article>
    @empty
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h3>No hay solicitudes pendientes</h3>
            <p>Todo está al día por ahora. ¡Buen trabajo!</p>
        </div>
    @endforelse
</div>

@endsection

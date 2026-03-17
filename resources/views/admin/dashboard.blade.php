@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <h2>Dashboard</h2>
    <p>Resumen de reservas y estadísticas generales.</p>

    {{-- ===== ESTADÍSTICAS RÁPIDAS ===== --}}
    <div class="stats">
        <div class="card">
            {{ $reservas }}
            <small>Reservas</small>
        </div>
        <div class="card">
            {{ $espaciosDisponibles }}
            <small>Espacios disponibles</small>
        </div>
        <div class="card">
            {{ $solicitudesPendientes }}
            <small>Solicitudes pendientes</small>
        </div>
    </div>

    {{-- ===== ÚLTIMAS RESERVAS ===== --}}
    <div class="latest-reservas" style="margin-top:20px;">
        <h3>Últimas reservas</h3>

        <table style="width:100%;border-collapse:collapse;box-shadow:0 2px 8px rgba(0,0,0,0.05);border-radius:8px;overflow:hidden;">
            <thead style="background:#f9fafb;text-align:left;">
                <tr>
                    <th>Cliente</th>
                    <th>Espacio</th>
                    <th>Hora inicio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimasReservas as $reserva)
                    <tr>
                        <td>{{ $reserva->usuario->user_nombre ?? 'N/D' }}</td>
                        <td>{{ $reserva->espacio->esp_nombre ?? 'N/D' }}</td>
                        <td>{{ $reserva->rsva_hora_inicio }}</td>
                        <td>{{ $reserva->rsva_estado }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:1rem;">No hay reservas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

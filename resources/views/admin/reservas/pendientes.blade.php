@extends('layouts.admin')

@section('title', 'Solicitudes Pendientes')
@section('page-title', 'Solicitudes de reservas')

@section('content')

<h2>Reservas Pendientes</h2>
<p>Solicitudes de reserva que aguardan aprobación.</p>

@if ($reservas->isEmpty())
    <p style="color:#888;">No hay solicitudes pendientes.</p>
@else
<div style="overflow-x:auto;">
<table class="tabla-reservas">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Espacio</th>
            <th>Fecha</th>
            <th>Hora inicio</th>
            <th>Hora fin</th>
            <th>Invitados</th>
            <th>Descripción</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reservas as $r)
        <tr>
            <td>{{ $r->reserva_id }}</td>
            <td>{{ $r->usuario->nombre ?? '—' }}</td>
            <td>{{ $r->espacio->esp_nombre ?? '—' }}</td>
            <td>{{ \Carbon\Carbon::parse($r->rsva_fecha)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($r->rsva_hora_inicio)->format('g:i A') }}</td>
            <td>{{ \Carbon\Carbon::parse($r->rsva_hora_fin)->format('g:i A') }}</td>
            <td>{{ $r->rsva_num_invitados }}</td>
            <td>{{ $r->rsva_descripcion }}</td>
            <td>
                <span class="badge badge-pendiente">{{ $r->rsva_estado }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif

@endsection

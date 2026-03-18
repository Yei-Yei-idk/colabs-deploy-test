@extends('layouts.admin')

@section('title', 'Solicitudes Pendientes')
@section('page-title', 'Solicitudes de reservas')

@section('styles')
<style>
    /* Estilos específicos para esta página (Rediseño Moderno) */
    .reservas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 24px;
        margin-top: 20px;
    }

    .reserva-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f3f4f6;
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
    }

    .reserva-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #facc15;
    }

    .reserva-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .reserva-id {
        font-size: 0.875px;
        font-weight: 600;
        color: #6b7280;
        background: #f3f4f6;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .reserva-badge {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 6px;
        letter-spacing: 0.025em;
    }

    .badge-pendiente {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
    }

    .user-email {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .space-info {
        background: #f9fafb;
        padding: 12px;
        border-radius: 12px;
        border-left: 4px solid #facc15;
    }

    .space-name {
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .reserva-details {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 0.9375rem;
        color: #4b5563;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .reserva-footer {
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f3f4f6;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-action {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-aceptar {
        background: #10b981;
        color: white;
    }

    .btn-aceptar:hover {
        background: #059669;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-rechazar {
        background: #ffffff;
        color: #ef4444;
        border: 1px solid #fecaca;
    }

    .btn-rechazar:hover {
        background: #fef2f2;
        border-color: #ef4444;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .empty-state h3 {
        color: #374151;
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #6b7280;
    }

    /* Contenedor de Alertas */
    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #a7f3d0;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')

@if(session('success'))
    <div class="alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
    </div>
@endif

<div class="page-intro" style="margin-bottom: 30px;">
    <h1 style="font-size: 2rem; color: #111827; margin-bottom: 8px;">Solicitudes de reservas</h1>
    <p style="color: #6b7280; font-size: 1.125rem;">Confirma las solicitudes de los clientes para asegurar su espacio.</p>
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
                <div style="font-size: 0.8125rem; color: #6b7280; margin-top: 4px;">
                    Capacidad: {{ $r->espacio->esp_capacidad ?? '?' }} Persona(s)
                </div>
            </div>
            
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
        <div class="empty-state" style="grid-column: 1 / -1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h3>No hay solicitudes pendientes</h3>
            <p>Todo está al día por ahora. ¡Buen trabajo!</p>
        </div>
    @endforelse
</div>

@endsection

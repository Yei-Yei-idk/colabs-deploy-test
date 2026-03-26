@extends('layouts.admin')

@section('title', 'Reservas')
@section('page-title', 'Reservas')

@section('content')

<h2>Reservas</h2>
<p>Listado de reservas de clientes.</p>

{{-- ── NAVEGACIÓN DE FECHAS ────────────────────────────────── --}}
<div class="navegacion-fechas">

    <a href="{{ route('admin.reservas.index', ['fecha' => $fechaAnterior]) }}"
       class="btn-nav">⬅ Día anterior</a>

    <form method="get" action="{{ route('admin.reservas.index') }}" style="display:inline;">
        <input type="date"
               class="input-fecha"
               name="fecha"
               value="{{ $fecha->format('Y-m-d') }}"
               onchange="this.form.submit()">
    </form>

    <a href="{{ route('admin.reservas.index', ['fecha' => $fechaSiguiente]) }}"
       class="btn-nav">Día siguiente ➡</a>

</div>

{{-- ── CALENDARIO DEL DÍA ──────────────────────────────────── --}}
<h2>Calendario del día {{ $fecha->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</h2>

<div style="overflow-x:auto;">
<table class="tabla-reservas">
    <thead>
        <tr>
            <th>Hora</th>
            @foreach ($espacios as $espacio)
                <th>{{ $espacio->esp_nombre }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @php
            $slotInicio = \Carbon\Carbon::parse('06:00:00');
            $slotFin    = \Carbon\Carbon::parse('21:00:00');
        @endphp

        @while ($slotInicio < $slotFin)

            @php
                $horaActual    = $slotInicio->format('H:i:s');
                $horaSiguiente = $slotInicio->copy()->addHour()->format('H:i:s');
            @endphp

            <tr>
                <td><strong>{{ $slotInicio->format('g:i A') }}</strong></td>

                @foreach ($espacios as $espacio)
                    @php
                        $ocupado          = false;
                        $reservaEncontrada = null;

                        if ($reservas->has($espacio->espacio_id)) {
                            foreach ($reservas[$espacio->espacio_id] as $r) {
                                $rsInicio = $r->rsva_hora_inicio; // 'HH:MM:SS' string
                                $rsFin    = $r->rsva_hora_fin;

                                // Solapamiento: el slot se superpone con la reserva
                                $hayOverlap = ($rsInicio < $horaSiguiente && $rsFin > $horaActual)
                                           || ($rsFin === $horaActual);

                                if ($hayOverlap) {
                                    $ocupado           = true;
                                    $reservaEncontrada = $r;
                                    break;
                                }
                            }
                        }
                    @endphp

                    @php
                        $claseCss = 'disponible';
                        if ($ocupado) {
                            // Normalizamos a minúsculas para comparar
                            $estado = strtolower($reservaEncontrada->rsva_estado);
                            $claseCss = ($estado == 'pendiente') ? 'pendiente' : 'reservado';
                        }
                    @endphp

                    <td class="{{ $claseCss }}"
                        data-espacio="{{ $espacio->espacio_id }}"
                        data-hora="{{ $horaActual }}"
                        @if ($ocupado)
                            data-reserva-id="{{ $reservaEncontrada->reserva_id }}"
                            data-user-name="{{ $reservaEncontrada->usuario->user_nombre ?? 'N/A' }}"
                            data-user-email="{{ $reservaEncontrada->usuario->user_correo ?? 'N/A' }}"
                            data-espacio-nombre="{{ $espacio->esp_nombre }}"
                            data-user-phone="{{ $reservaEncontrada->usuario->user_telefono ?? 'Sin número' }}"
                            data-hora-inicio="{{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_inicio)->format('g:i A') }}"
                            data-hora-fin="{{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_fin)->format('g:i A') }}"
                            data-hora-fin-raw="{{ $reservaEncontrada->rsva_hora_fin }}"
                            data-fecha="{{ $reservaEncontrada->rsva_fecha }}"
                        @endif>

                        @if ($ocupado)
                            @if(strtolower($reservaEncontrada->rsva_estado) == 'pendiente')
                                Pendiente<br>
                            @else
                                Ocupado<br>
                            @endif
                            <small>
                                ({{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_inicio)->format('g:i A') }}
                                 –
                                {{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_fin)->format('g:i A') }})
                            </small>
                            <span class="countdown-badge"
                                  data-fin="{{ $reservaEncontrada->rsva_fecha }} {{ $reservaEncontrada->rsva_hora_fin }}">
                            </span>
                        @else
                            Disponible
                        @endif

                    </td>
                @endforeach
            </tr>

            @php $slotInicio->addHour(); @endphp

        @endwhile
    </tbody>
</table>

{{-- MODAL PARA CAMBIAR HORA DE FIN --}}
<div id="modal-hora" class="modal-overlay" style="display:none;">
    <div class="modal-content animate-pop">
        <h3 style="margin-top:0;">Actualizar Fin de Reserva</h3>
        <p>¿A qué hora deseas que finalice esta reserva?</p>
        <form action="{{ route('admin.reservas.actualizar_hora') }}" method="POST">
            @csrf
            <input type="hidden" name="reserva_id" id="modal-reserva-id">
            <input type="time" name="nueva_hora_fin" id="modal-hora-fin" class="input-hora-grande" required>
            
            <div class="modal-actions" style="margin-top: 20px; display:flex; gap:10px; justify-content:center;">
                <button type="button" class="btn-cancel" onclick="cerrarModalHora()">Cancelar</button>
                <button type="submit" class="btn-save">Guardar Cambio</button>
            </div>
        </form>
    </div>
</div>

</div>

<style>
/* CSS del Contador y Modal integrado aquí para asegurar que cargue */
.countdown-badge {
    display: inline-block;
    margin-top: 5px;
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
    color: #fff;
    cursor: pointer;
    transition: transform 0.2s, background 0.3s;
}
.countdown-badge:hover {
    transform: scale(1.05);
}
.cd-green { background-color: #10b981; }  /* > 30 min */
.cd-yellow { background-color: #f59e0b; color: #fff;} /* < 30 min */
.cd-red { background-color: #ef4444; }    /* < 5 min */
.cd-expired { background-color: #6b7280; } /* Vencida */

.modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000;
    display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);
}
.modal-content {
    background: white; border-radius: 12px; padding: 25px; width: 90%; max-width: 350px;
    text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.animate-pop { animation: popIn 0.3s ease-out; }
@keyframes popIn { from { transform: scale(0.9); opacity:0; } to { transform: scale(1); opacity:1; } }

.input-hora-grande {
    font-size: 1.5rem; padding: 10px; width: 80%; border: 2px solid #ccc; border-radius: 8px; margin: 10px 0;text-align: center;
}
.btn-cancel { background: #9ca3af; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;}
.btn-save { background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. CRONÓMETRO REGRESIVO (COUNTDOWN)
    function actualizarContadores() {
        const badges = document.querySelectorAll('.countdown-badge');
        const ahora = new Date();

        badges.forEach(badge => {
            const fechaFinStr = badge.dataset.fin; // YYYY-MM-DD HH:MM:SS
            if (!fechaFinStr) return;

            // Arreglar compatibilidad de fecha en Safari/iOS (reemplazar guiones por slashes)
            const f = new Date(fechaFinStr.replace(/-/g, '/'));
            const diffMs = f - ahora;

            if (diffMs <= 0) {
                badge.textContent = "⏱ Tiempo Agotado";
                badge.className = "countdown-badge cd-expired";
            } else {
                const diffMinutos = Math.floor(diffMs / 60000);
                const hrs = Math.floor(diffMinutos / 60);
                const mins = diffMinutos % 60;
                
                let text = "⏱ Quedan ";
                if(hrs > 0) text += `${hrs}h ${mins}m`;
                else text += `${mins}m`;
                
                badge.textContent = text;

                // Colores
                if (diffMinutos > 30) badge.className = "countdown-badge cd-green";
                else if (diffMinutos > 5) badge.className = "countdown-badge cd-yellow";
                else badge.className = "countdown-badge cd-red";
            }
        });
    }

    // Ejecutar contador inmediatamente y luego cada 10 segundos
    actualizarContadores();
    setInterval(actualizarContadores, 10000);

    // 2. MODAL DE CAMBIO DE HORA
    window.cerrarModalHora = function() {
        document.getElementById('modal-hora').style.display = 'none';
    }

    const celdasOcupadas = document.querySelectorAll('.tabla-reservas td[data-reserva-id]');
    celdasOcupadas.forEach(celda => {
        // Al darle click a una celda reservada en el calendario, abre el modal
        celda.addEventListener('click', (e) => {
            const reservaId = celda.dataset.reservaId;
            const horaFinRaw = celda.dataset.horaFinRaw; // HH:MM:SS
            
            if(reservaId && horaFinRaw) {
                document.getElementById('modal-reserva-id').value = reservaId;
                // HH:MM:SS -> HH:MM para el input type="time"
                document.getElementById('modal-hora-fin').value = horaFinRaw.substring(0, 5);
                document.getElementById('modal-hora').style.display = 'flex';
            }
        });
        
        // Quitar el cursor pointer si esta disponible para que quede claro que solo ocupados son clickeables
        celda.style.cursor = 'pointer';
    });
});
</script>

<script src="{{ asset('js_admin/calendario.js') }}"></script>
@endsection

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

                    <td class="{{ $ocupado ? 'reservado' : 'disponible' }}"
                        data-espacio="{{ $espacio->espacio_id }}"
                        data-hora="{{ $horaActual }}"
                        @if ($ocupado)
                            data-reserva-id="{{ $reservaEncontrada->reserva_id }}"
                            data-user-name="{{ $reservaEncontrada->usuario->user_nombre ?? 'N/A' }}"
                            data-user-email="{{ $reservaEncontrada->usuario->user_correo ?? 'N/A' }}"
                            data-espacio-nombre="{{ $espacio->esp_nombre }}"
                            data-hora-inicio="{{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_inicio)->format('g:i A') }}"
                            data-hora-fin="{{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_fin)->format('g:i A') }}"
                        @endif>

                        @if ($ocupado)
                            Ocupado<br>
                            <small>
                                ({{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_inicio)->format('g:i A') }}
                                 –
                                {{ \Carbon\Carbon::parse($reservaEncontrada->rsva_hora_fin)->format('g:i A') }})
                            </small>
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
<script src="{{ asset('js_admin/calendario.js') }}"></script>
</div>

@endsection

{{-- resources/views/inicio.blade.php --}}
@extends('layouts.app')

@section('title', 'Colabs — Espacios de trabajo')

@section('content')

{{-- ===== SECCIÓN INICIO ===== --}}
<section id="inicio" class="section active">

    {{-- Card: ¿Qué es Colabs? --}}
    <div class="container">
        <h2>¿Qué es Colabs?</h2>
        <p>
            Somos una empresa de alquiler de espacios —salones, oficinas y más—
            pensada para que otras empresas nunca se queden sin zonas de trabajo.
            Contamos con cientos de espacios, la mayoría ya amoblados, listos para
            que tu equipo empiece a trabajar desde el primer día.
        </p>
    </div>

    {{-- Grid de espacios --}}
    <section class="espacios">
        <h2>Conoce nuestros espacios &rsaquo;&rsaquo;</h2>

        @foreach ($espacios as $espacio)
            <div class="espacio {{ $espacio['invertido'] ? 'invertido' : '' }}">

                @unless ($espacio['invertido'])
                    <img
                        src="{{ asset($espacio['imagen']) }}"
                        alt="{{ $espacio['alt'] }}"
                        loading="lazy"
                        width="440" height="300"
                    >
                @endunless

                <div class="texto">
                    <h3>| {{ $espacio['titulo'] }} |</h3>
                    <p>{{ $espacio['desc'] }}</p>
                    <a href="{{ $espacio['link'] }}">Ver más</a>
                </div>

                @if ($espacio['invertido'])
                    <img
                        src="{{ asset($espacio['imagen']) }}"
                        alt="{{ $espacio['alt'] }}"
                        loading="lazy"
                        width="440" height="300"
                    >
                @endif

            </div>
        @endforeach

    </section>

</section>

@endsection
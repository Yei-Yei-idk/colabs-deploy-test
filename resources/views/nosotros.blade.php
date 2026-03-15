@extends('layouts.app')

@section('title', 'Nosotros - Colabs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nosotros.css') }}">
@endpush

@section('content')

<section class="mision">
    <h2>MISIÓN</h2>
    <div class="info">
        <p>
            Nuestra misión es dar un servicio y un espacio de trabajo cómodo para distintas empresas,
            sin importar cuánto tiempo tengas disponible, siempre habrá un espacio para ti en Colabs.
        </p>
    </div>
</section>

<section class="vision">
    <h2>VISIÓN</h2>
    <div class="info">
        <p>
            Nuestra visión es que te concentres en tu trabajo en un ambiente cómodo,
            con cero estrés y listo para empezar tu jornada laboral. Queremos hacer
            del coworking una metodología más eficaz para que las empresas trabajen
            con confianza en espacios seguros.
        </p>
    </div>
</section>

<section class="porque">
    <h1>¿POR QUÉ <span>ELEGIRNOS?</span></h1>

    <p>
        <span class="highlight">Sin problemas al alquilar</span>, olvídate de largas
        negociaciones y de contratos complicados.
    </p>

    <div class="columnas-container">
        <div class="columna-item">
            <h2>INFRAESTRUCTURA <span>PROFESIONAL</span></h2>
            <p>Nos encargamos de todo para que tú solo te preocupes de tus proyectos.</p>
        </div>

        <div class="columna-item">
            <h2>AMBIENTE <span>COLABORATIVO</span></h2>
            <p>Conéctate a nuestra red de más de 500 profesionales y amplía tus contactos.</p>
        </div>

        <div class="columna-item">
            <h2>FLEXIBILIDAD <span>TOTAL</span></h2>
            <p>Encuentra el espacio que mejor se adapte a tus necesidades.</p>
        </div>
    </div>
</section>

@endsection
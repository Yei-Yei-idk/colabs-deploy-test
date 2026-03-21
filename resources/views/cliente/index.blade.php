@extends('layouts.cliente')

@section('title', 'Inicio - Colabs')


@section('content')

    <div class="hero animate-fade-up">
        <div class="hero-text">
            <h2>Coworking en Barranquilla</h2>
            <p>En COLABS encuentras oficinas modernas, escritorios compartidos y espacios para reuniones, todo en un
                ambiente cómodo, colaborativo y con todos los servicios que necesitas.</p>
            <div class="beneficios">
                <div>
                    <h3>¿Necesitas un espacio solo por unas horas?</h3>
                    <p>Reserva el tiempo que necesites</p>
                    <p>Elige tu horario y asegura tu lugar sin complicaciones.</p>
                </div>
                <div>
                    <h3>¿Tienes una reunión importante?</h3>
                    <p>Contamos con salas de reuniones privadas</p>
                    <p>Ambientes privados, cómodos y con todo lo necesario para tu equipo o clientes.</p>
                </div>
            </div>
            <a href="{{ route('cliente.buscar_espacios') }}" class="btn-principal">Ver oficinas ></a>
        </div>
        <div class="hero-img"></div>
    </div>
    
    <h2 id="subtitulo_espacios" class="mis-reservas-header mt-20 mb-20 animate-fade-up" style="animation-delay: 0.2s;">Espacios Destacados</h2>
    
    <div class="slider-container animate-fade-up" style="animation-delay: 0.4s;">
        <button class="prev">‹</button>
        <div class="slider-wrapper">
            <div class="slider">
                @forelse($espacios as $espacio)
                    @php
                        $imgSrc = $espacio->imagen ? $espacio->imagen->foto : 'default.jpg';
                    @endphp
                    <div class="slide-card">
                        <img src="{{ asset('uploads/' . $imgSrc) }}" alt="{{ $espacio->esp_nombre }}" data-fallback="{{ asset('uploads/OF1 .jpeg') }}" onerror="this.src=this.getAttribute('data-fallback')">
                        <h3>{{ $espacio->esp_nombre }}</h3>
                        <p>{{ $espacio->esp_descripcion ?? 'Espacio ideal para tus proyectos.' }}</p>
                    </div>
                @empty
                    <!-- Fallback si la BD aún está vacía -->
                    <div class="slide-card">
                        <img src="{{ asset('uploads/OF1 .jpeg') }}" alt="Oficina 1">
                        <h3>Oficina Privada Industrial y Contemporánea</h3>
                        <p>Diseño industrial moderno, ideal para empresas o equipos profesionales.</p>
                    </div>

                    <div class="slide-card">
                        <img src="{{ asset('uploads/OF 3.jpeg') }}" alt="Oficina 2">
                        <h3>Oficina Privada Moderna y Minimalista</h3>
                        <p>Diseño moderno y minimalista, ideal para productividad y comodidad.</p>
                    </div>

                    <div class="slide-card">
                        <img src="{{ asset('uploads/OF 13.jpg') }}" alt="Oficina 3">
                        <h3>Oficina Compartida Equipada y Luminosa</h3>
                        <p>Múltiples estaciones de trabajo, ideal para equipos colaborativos.</p>
                    </div>

                    <div class="slide-card">
                        <img src="{{ asset('uploads/Ofic 8.jpeg') }}" alt="Oficina 4">
                        <h3>Sala de Reuniones Ejecutiva</h3>
                        <p>Espacio privado y profesional para juntas importantes.</p>
                    </div>

                    <div class="slide-card">
                        <img src="{{ asset('uploads/OF12.jpeg') }}" alt="Oficina 5">
                        <h3>Escritorio Colaborativo</h3>
                        <p>Espacio abierto perfecto para freelancers y emprendedores.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <button class="next">›</button>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/cliente/esp_destacados.js') }}"></script>
@endsection
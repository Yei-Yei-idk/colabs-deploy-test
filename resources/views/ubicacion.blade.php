@extends('layouts.app')

@section('title', 'Ubicación - Colabs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ubicacion.css') }}">
@endpush

@section('content')

<div class="sede-info">
    <h1>Nuestra Sede</h1>
    <p>
        Descubre un espacio diseñado para inspirar.
        En el corazón de la ciudad, nuestra sede ofrece un ambiente moderno, cómodo y funcional donde la
        creatividad fluye y las ideas se convierten en proyectos reales.
    </p>
    <h1>| Unétenos |</h1>
</div>

<h2 style="text-align: center; margin-top: 40px;">Ubicación De Nuestra Sede</h2>

<div class="ubicacion-grid">
    <!-- Columna izquierda: Mapa -->
    <div class="columna-mapa">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d489.55124333187763!2d-74.80165236599272!3d11.007837464815433!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8ef42d98f0a452cb%3A0x227b4d2ce9658001!2sColabs%20Coworking%20Spaces!5e0!3m2!1ses-419!2sco!4v1758286282455!5m2!1ses-419!2sco"
            width="100%" height="300" style="border:0; border-radius:10px;" allowfullscreen="" loading="lazy">
        </iframe>
        <h3>¡Ven y Búscanos!</h3>
        <p>
            Tu próximo gran proyecto comienza con una visita. <br>
            Acércate y vive la experiencia Colabs.
            <br><br>¡No te lo contamos, vívelo. Te esperamos!
        </p>
    </div>

    <!-- Columna derecha: Info -->
    <div class="columna-info">
        <h3>| Sede Centro Histórico |</h3>
        <p>Cl. 77 #59-85 piso 2, Nte. Centro Histórico, Barranquilla, Atlántico</p>
        <!-- Uso de asset para imágenes -->
        <img src="{{ asset('ASSETS/image 4.png') }}" alt="Foto de la sede">
    </div>
</div>

<!-- Redes -->
<div class="redes">
    <h2>Conoce nuestras Redes</h2>
    
    <div class="red-item">
        <h3>| Instagram |</h3>
        <a href="#">
             <!-- Asegúrate de tener estas imágenes en public/ASSETS/ -->
            <img src="{{ asset('ASSETS/image 9.png') }}" alt="Instagram Colabs">
        </a>
    </div>
    
    <hr style="width: 50%; margin: 20px auto;">
    
    <div class="red-item">
        <h3>| TikTok |</h3>
        <a href="#">
            <img src="{{ asset('ASSETS/image 11.png') }}" alt="TikTok Colabs">
        </a>
    </div>
</div>

@endsection
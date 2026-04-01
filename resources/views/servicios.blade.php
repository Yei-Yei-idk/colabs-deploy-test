@extends('layouts.app')

@section('title', 'Servicios - Colabs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/servicios.css') }}">
@endpush
@section('content')

<div class="hero-servicios">
    <!-- Ajusté la ruta de la imagen, asegúrate que img1_servicios.jpg esté en public/ASSETS/ -->
    <img src="{{ asset('ASSETS/img1_servicios.jpg') }}" alt="Servicios Colabs">
    <div class="hero-text">El mejor espacio para desarrollar tu trabajo</div>
</div>

<br><br>
<center>
    <h2>| ESPACIOS DE COLABS |</h2>
</center>

<div class="grid-espacios">
    @forelse($espacios as $espacio)
        <article class="espacio-card">
            <img src="{{ asset('ASSETS/' . ($espacio->imagen->nombre_archivo ?? 'default.jpg')) }}" alt="{{ $espacio->esp_nombre }}" loading="lazy">
            <h3>| {{ strtoupper($espacio->esp_nombre) }} |</h3>
            <p>{{ $espacio->esp_descripcion }}</p>
        </article>
    @empty
        <p style="color:#333;">No hay espacios disponibles en este momento.</p>
    @endforelse
</div>

<div class="container-benef">
    <h2 style="text-align: center; color: #FFD600;">| SERVICIOS EXTRAS |</h2>
    
    <div class="extras-grid">
        <div class="extra-item">
            <h3>Zonas Comunes</h3>
            <img src="{{ asset('ASSETS/espacios descanso.png') }}">
        </div>
        <div class="extra-item">
            <h3 style="color: #FFD600;">Limpieza Diaria</h3>
            <img src="{{ asset('ASSETS/limpieza.jpg') }}">
        </div>
        <div class="extra-item">
            <h3>Internet Alta Velocidad</h3>
            <img src="{{ asset('ASSETS/internet.png') }}">
        </div>
    </div>
</div>

@endsection
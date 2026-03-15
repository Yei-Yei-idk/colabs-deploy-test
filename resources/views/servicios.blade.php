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
    <article class="espacio-card">
        <img src="{{ asset('ASSETS/OF12.jpeg') }}" alt="Oficinas personales" loading="lazy">
        <h3>| OFICINAS PERSONALES |</h3>
    </article>
    <article class="espacio-card">
        <img src="{{ asset('ASSETS/ofic 11.jpeg') }}" alt="Sala de reuniones" loading="lazy">
        <h3>| SALAS DE REUNIONES |</h3>
    </article>
    <article class="espacio-card">
        <img src="{{ asset('ASSETS/Of 14 puestos de trabajo .jpeg') }}" alt="Oficinas compartidas" loading="lazy">
        <h3>| OFICINAS COMPARTIDAS |</h3>
    </article>
    <article class="espacio-card">
        <!-- Ojo con el nombre de este archivo, tiene espacios -->
        <img src="{{ asset('ASSETS/WhatsApp Image 2025-09-05 at 11.24.18 AM.jpeg') }}" alt="Cafetería" loading="lazy">
        <h3>| CAFETERÍA |</h3>
    </article>
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
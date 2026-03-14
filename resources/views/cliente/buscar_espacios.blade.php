@extends('layouts.cliente')

@section('title', 'Buscar Espacios - COLABS')

@section('content')
<section id="buscar" class="section active">
    <!-- Este `<center>` en un futuro lo optimizaremos con CSS, lo mantengo temporal para que no pierdas estructura visual -->
    <center>
        <h2>Buscar Espacios</h2>
    </center>

    <div class="buscar-container">
        <!-- ✅ FORMULARIO DE FILTROS -->
        <form method="GET" action="{{ route('cliente.buscar_espacios') }}" class="form-filtros">
            <aside class="sidebar">
                <h3>Filtrar Espacios</h3>

                <!-- 🔹 FILTRO TIPO -->
                <div class="filtro">
                    <label for="tipo">Tipo de espacio:</label>
                    <select name="esp_tipo" id="tipo">
                        <option value="">Todos</option>
                        <option value="Oficina" {{ $tipo == 'Oficina' ? 'selected' : '' }}>Oficina</option>
                        <option value="Sala de reuniones" {{ $tipo == 'Sala de reuniones' ? 'selected' : '' }}>Sala de reuniones</option>
                        <option value="Sala de eventos" {{ $tipo == 'Sala de eventos' ? 'selected' : '' }}>Sala de eventos</option>
                        <option value="Aula" {{ $tipo == 'Aula' ? 'selected' : '' }}>Aula</option>
                    </select>
                </div>

                <!-- 🔹 FILTRO CAPACIDAD -->
                <div class="filtro">
                    <label for="capacidad">Capacidad máxima:</label>
                    <select name="esp_capacidad" id="capacidad">
                        <option value="">Todas</option>
                        <option value="5" {{ $capacidad == '5' ? 'selected' : '' }}>Hasta 5 personas</option>
                        <option value="10" {{ $capacidad == '10' ? 'selected' : '' }}>Hasta 10 personas</option>
                        <option value="15" {{ $capacidad == '15' ? 'selected' : '' }}>Hasta 15 personas</option>
                        <option value="20" {{ $capacidad == '20' ? 'selected' : '' }}>Hasta 20 personas</option>
                    </select>
                </div>

                <!-- 🔹 FILTRO PRECIO -->
                <div class="filtro">
                    <label for="precio">Precio máximo por hora:</label>
                    <select name="esp_precio_hora" id="precio">
                        <option value="">Sin límite</option>
                        <option value="20000" {{ $precioMax == '20000' ? 'selected' : '' }}>Hasta $20.000</option>
                        <option value="50000" {{ $precioMax == '50000' ? 'selected' : '' }}>Hasta $50.000</option>
                        <option value="100000" {{ $precioMax == '100000' ? 'selected' : '' }}>Hasta $100.000</option>
                        <option value="200000" {{ $precioMax == '200000' ? 'selected' : '' }}>Hasta $200.000</option>
                    </select>
                </div>

                <button type="submit" class="btn-filtrar btn-principal aplicar-filtros">Aplicar Filtros</button>
            </aside>
        </form>

        <!-- 🔹 LISTADO DE ESPACIOS -->
        <div class="espacios-listado" style="width: 100%;">
            @forelse ($espacios as $espacio)
                @php
                    // Resolver la imagen a mostrar
                    $imgSrc = $espacio->imagen ? $espacio->imagen->foto : 'default.jpg';
                @endphp
                <div class="espacio-card">
                    <!-- Si tienes una carpeta llamada `Actualizados_Super/Espacios/` en public, úsala así: -->
                    {{-- <img src="{{ asset('Actualizados_Super/Espacios/' . $imgSrc) }}" alt="{{ $espacio->esp_nombre }}"> --}}
                    
                    <!-- Temporal, usando el asset default asumiendo migración progresiva -->
                    <img src="{{ asset('ASSETS/Imagenes oficinas/OF1 .jpeg') }}" alt="{{ $espacio->esp_nombre }}">
                    
                    <div class="espacio-info">
                        <h3>{{ $espacio->esp_nombre }}</h3>
                        <p>{{ $espacio->esp_descripcion }}</p>
                        <p><strong>Capacidad:</strong> {{ $espacio->esp_capacidad }} personas</p>
                        <p><strong>Tipo:</strong> {{ $espacio->esp_tipo }}</p>
                        <p><strong>Precio por hora:</strong> ${{ number_format($espacio->esp_precio_hora, 0, ',', '.') }} COP</p>

                        <!-- La ruta futura para reservar. Asumiendo que crearás una ruta como cliente.reservar -->
                        <a href="{{ url('cliente/reservar/' . $espacio->espacio_id) }}" class="btn-reservar">Reservar ahora</a>
                    </div>
                </div>
            @empty
                <p>No hay espacios que coincidan con los filtros seleccionados.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection

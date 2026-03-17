@extends('layouts.admin')

@section('title', 'Editar Espacio')
@section('page-title', 'Espacios')

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2>Editar espacio</h2>
            <p>Edite un espacio de coworking mediante el siguiente formulario</p>
        </div>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORMULARIO --}}
        <form method="POST" action="{{ route('admin.espacios.update', $espacio->espacio_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- INFORMACIÓN BÁSICA --}}
                <div class="form-section">
                    <h3>Información Básica</h3>

                    <div class="form-group">
                        <label for="nombre">Nombre del Espacio *</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $espacio->esp_nombre) }}" placeholder="Ej. Sala de Innovación" required>
                    </div>

                    <div class="form-group">
                        <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 15px;">Tipo de oficina</h4>
                        <div class="radio-group">
                            @php
                                $tipos = [
                                    'Oficina' => ['Oficina', 'Espacio privado para trabajo individual'],
                                    'Sala de reuniones' => ['Sala de Reuniones', 'Espacio ideal para reuniones de equipo y juntas'],
                                    'Sala de eventos' => ['Sala de Eventos', 'Espacio para conferencias y eventos'],
                                    'Aula' => ['Aula', 'Espacio equipado para capacitaciones']
                                ];
                            @endphp

                            @foreach($tipos as $valor => $info)
                                <div class="radio-option">
                                    <input type="radio" id="{{ str_replace(' ', '_', $valor) }}" name="tipo_oficina" value="{{ $valor }}"
                                        {{ trim(old('tipo_oficina', $espacio->esp_tipo)) == $valor ? 'checked' : '' }} required>
                                    <div class="radio-content">
                                        <div class="title">{{ $info[0] }}</div>
                                        <div class="description">{{ $info[1] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- DETALLES DEL ESPACIO --}}
                <div class="form-section">
                    <h3>Detalles del espacio</h3>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" placeholder="Describe las características, equipamiento y servicios disponibles" required>{{ old('descripcion', $espacio->esp_descripcion) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="aforo">Aforo Máximo *</label>
                        <div class="aforo-container">
                            <input type="number" id="aforo" name="capacidad" value="{{ old('capacidad', $espacio->esp_capacidad) }}" min="1" max="50" required>
                            <span>personas</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Precio_hora">Precio por hora *</label>
                        <div class="aforo-container">
                            <input type="number" name="Precio_hora" required value="{{ old('Precio_hora', $espacio->esp_precio_hora) }}" placeholder="$" required>
                            <span>COP</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Imagen del espacio (Opcional)</label>
                        <input type="file" name="foto" id="foto" accept="image/*">
                        <p style="font-size: 12px; color: #666; margin-top: 5px;">Deja en blanco para mantener la imagen actual.</p>
                    </div>
                </div>
            </div>

            <div class="form-confirmation">
                <p><strong>¿Confirmó que todos los datos son correctos?</strong></p>
                <p>Pulse 'Editar espacio' para finalizar.</p>
            </div>

            <button type="submit" class="btn-crear">Editar espacio</button>
        </form>
    </div>
@endsection

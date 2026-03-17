@extends('layouts.admin')

@section('title', 'Espacios')
@section('page-title', 'Espacios')

@section('content')
    <h2>Espacios</h2>
    <p>Administra los espacios de coworking disponibles</p>

    {{-- ESTADÍSTICAS --}}
    <div class="stats">
        <div class="card">{{ $total_espacios }} <small>Total espacios</small></div>
        <div class="card">{{ $total_espacios_activos }} <small>Activos</small></div>
        <div class="card">{{ $total_espacios_inactivos }} <small>Inactivos</small></div>
    </div>

    {{-- FILTROS Y BOTÓN NUEVO --}}
    <div class="filters">
        <select>
            <option>Todos</option>
        </select>
        <select>
            <option>Tipo espacio</option>
        </select>
        <input type="text" placeholder="Buscar...">
        
        @if(auth()->user()->rol_id == 1)
            <a href="{{ route('admin.espacios.create') }}" class="new-btn" style="text-decoration: none;">Nuevo espacio</a>
        @endif
    </div>

    {{-- TABLA --}}
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Espacio</th>
                <th>Descripción</th>
                <th>Capacidad</th>
                <th>Tipo</th>
                <th>Precio hora</th>
                <th>Estado</th>
                @if(auth()->user()->rol_id == 1)
                    <th>Acción</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($espacios as $espacio)
                <tr>
                    <td>{{ $espacio->espacio_id }}</td>
                    <td>{{ $espacio->esp_nombre }}</td>
                    <td>{{ $espacio->esp_descripcion }}</td>
                    <td>{{ $espacio->esp_capacidad }}</td>
                    <td>{{ $espacio->esp_tipo }}</td>
                    <td>${{ number_format($espacio->esp_precio_hora, 0, ',', '.') }}</td>
                    <td>
                        <span class="status {{ strtolower($espacio->esp_estado) == 'activo' ? 'active' : 'inactive' }}"></span>
                        {{ $espacio->esp_estado }}
                    </td>
                    @if(auth()->user()->rol_id == 1)
                        <td>
                            {{-- Botón Editar --}}
                            <a href="{{ route('admin.espacios.edit', $espacio->espacio_id) }}" class="accion-btn" title="Editar">✏️</a>

                            {{-- Formulario para cambiar estado --}}
                            <form action="{{ route('admin.espacios.toggle', $espacio->espacio_id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="accion-btn" title="Cambiar estado" style="background: none; border: none; cursor: pointer;">
                                    @if($espacio->esp_estado == 'Activo')
                                        ❌
                                    @else
                                        ✅
                                    @endif
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Snackbar heredado del layout --}}
@endsection

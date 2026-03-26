@extends('layouts.admin')

@section('title', 'Copia de seguridad')
@section('page-title', 'Copias de seguridad')

@section('content')
    <div class="backup-container">
        <div class="backup-header">
            <h2>Copias de seguridad</h2>
            <p>Haz y carga copias de seguridad de la información de Colabs</p>
        </div>

        <div class="backup-buttons">
            <button type="button" class="backup-btn" onclick="mostrarFormulario()">Crear copia de seguridad</button>
            <button type="button" class="backup-btn">Cargar copia de seguridad</button>
        </div>

        <!-- FORMULARIO OCULTO -->
        <div id="backup-form" style="display:none; margin-top:20px;">
            <form action="{{ route('admin.backup.create') }}" method="POST">
                @csrf
                <p>¿Estás seguro de que deseas crear una copia de seguridad?</p>
                <button type="submit" class="backup-btn">Sí, crear copia</button>
                <button type="button" class="backup-btn" onclick="ocultarFormulario()">Cancelar</button>
            </form>
        </div>

        <!-- MENSAJES DE ESTADO -->
        <div class="backup-messages" style="margin-top: 30px;">
            @if(session('success'))
                <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px; animation: fadeInUp 0.3s ease;">
                    <span style="font-size: 1.2rem;">✅</span>
                    <span style="font-weight: 500;">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 12px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px; animation: fadeInUp 0.3s ease;">
                    <span style="font-size: 1.2rem;">❌</span>
                    <span style="font-weight: 500;">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <script>
            function mostrarFormulario() {
                document.getElementById('backup-form').style.display = 'block';
            }

            function ocultarFormulario() {
                document.getElementById('backup-form').style.display = 'none';
            }
        </script>
    </div>
@endsection
@extends('layouts.cliente')

@section('title', 'Mi Perfil - COLABS')

@section('content')
<section id="mi-perfil" class="section active">
    <h1 class="Titulo_perfil">Mi Perfil</h1>

    @if(session('success'))
        <div class="alert alert-success perfil-form-container">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error perfil-form-container">
            <ul class="m-0 pl-20">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="perfil-container">
        <div class="perfil-left">
            <img src="{{ asset('ASSETS/icon.webp') }}" alt="Foto de Perfil Usuario" class="Foto_usuario">
            <h3>{{ $usuario->user_nombre ?? $usuario->name ?? 'Usuario' }}</h3>
            <p class="correo">{{ $usuario->user_correo ?? $usuario->email ?? '' }}</p>
            <p class="verificado">✓ Cuenta Verificada</p>
        </div>

        <div class="perfil-right">
            <form id="formPerfil" method="POST" action="{{ route('cliente.perfil.actualizar') }}">
                @csrf
                <div class="bloque">
                    <h3>Información Personal</h3>
                    <button type="button" class="editar-bloque">✏ Editar</button>
                    
                    <div class="campo">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Escribe tu nombre" value="{{ $usuario->user_nombre ?? $usuario->name ?? '' }}" disabled required>
                    </div>
                    <div class="campo">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" value="{{ $usuario->user_correo ?? $usuario->email ?? '' }}" disabled required>
                    </div>
                    <div class="campo">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="+57 300 000 0000" value="{{ $usuario->user_telefono ?? '' }}" disabled required>
                    </div>
                    
                    <button type="submit" class="btn-guardar w-full mt-15">Guardar Cambios</button>
                </div>

                <div class="bloque">
                    <h3>Seguridad</h3>
                    <div class="campo">
                        <label for="password">Contraseña Actual</label>
                        <input type="password" id="password" name="password" placeholder="********" disabled>
                    </div>
                    <div class="campo">
                        <label for="newpassword">Nueva Contraseña</label>
                        <input type="password" id="newpassword" name="newpassword" placeholder="********" disabled>
                    </div>
                    <div class="campo">
                        <label for="confirmpassword">Confirmar Contraseña</label>
                        <input type="password" id="confirmpassword" name="confirmpassword" placeholder="********" disabled>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('js/cliente/perfil.js') }}"></script>
@endsection

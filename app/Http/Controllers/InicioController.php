<?php

namespace App\Http\Controllers;

class InicioController extends Controller
{
    public function index()
    {
        $espacios = [
            [
                'titulo'    => 'OFICINAS PERSONALES',
                'desc'      => 'Espacio para una persona, cómodo.',
                'imagen'    => 'imagenes/oficinas-personal.png',
                'alt'       => 'Oficinas personales',
                'link'      => route('cliente.buscar_espacios'),
                'invertido' => false,
            ],
            [
                'titulo'    => 'OFICINAS COMPARTIDAS',
                'desc'      => 'Sala más amplia para varios trabajadores.',
                'imagen'    => 'imagenes/oficinas-compartidas.png',
                'alt'       => 'Oficinas compartidas',
                'link'      => route('cliente.buscar_espacios'),
                'invertido' => true,
            ],
            [
                'titulo'    => 'SALAS DE REUNIONES',
                'desc'      => 'Espacios modernos con todos los servicios.',
                'imagen'    => 'imagenes/salas-reuniones.png',
                'alt'       => 'Salas de reuniones',
                'link'      => route('cliente.buscar_espacios'),
                'invertido' => false,
            ],
            [
                'titulo'    => 'CAFETERÍA',
                'desc'      => 'Todos los trabajadores merecen un descanso.',
                'imagen'    => 'imagenes/cafeteria.png',
                'alt'       => 'Cafetería',
                'link'      => route('cliente.buscar_espacios'),
                'invertido' => true,
            ],
        ];

        return view('inicio', compact('espacios'));
    }

    public function nosotros()
    {
        return view('nosotros');
    }

    public function ubicacion()
    {
        return view('ubicacion');
    }

    public function servicios()
    {
        return view('servicios');
    }
}
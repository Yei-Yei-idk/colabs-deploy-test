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
                'imagen'    => 'OF12.jpeg',
                'alt'       => 'Oficinas personales',
                'link'      => route('cliente.buscar_espacios'),
                'invertido' => false,
            ],
            [
                'titulo'    => 'OFICINAS COMPARTIDAS',
                'desc'      => 'Sala más amplia para varios trabajadores.',
                'imagen'    => 'Of 14 puestos de trabajo .jpeg',
                'alt'       => 'Oficinas compartidas',
                'link'      => route('cliente.buscar_espacios'),
                'invertido' => true,
            ],
            [
                'titulo'    => 'SALAS DE REUNIONES',
                'desc'      => 'Espacios modernos con todos los servicios.',
                'imagen'    => 'ofic 11.jpeg',
                'alt'       => 'Salas de reuniones',
                'link'      => route('cliente.buscar_espacios'),
                'invertido' => false,
            ],
            [
                'titulo'    => 'CAFETERÍA',
                'desc'      => 'Todos los trabajadores merecen un descanso.',
                'imagen'    => 'WhatsApp Image 2025-09-05 at 11.24.18 AM.jpeg',
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
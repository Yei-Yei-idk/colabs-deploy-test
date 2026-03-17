<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $espacios = [
            [
                'espacio_id' => 101,
                'esp_nombre' => 'Sala Innovación Alpha',
                'esp_descripcion' => 'Un espacio moderno diseñado para fomentar la creatividad y el trabajo colaborativo de alto impacto.',
                'esp_capacidad' => 8,
                'esp_tipo' => 'Sala de reuniones',
                'esp_precio_hora' => 45000,
                'esp_estado' => 'Activo',
                'imagen' => 'OF 13.jpg'
            ],
            [
                'espacio_id' => 102,
                'esp_nombre' => 'Oficina Ejecutiva Zen',
                'esp_descripcion' => 'Privacidad y confort en un ambiente minimalista ideal para concentrarse en tareas individuales.',
                'esp_capacidad' => 2,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 30000,
                'esp_estado' => 'Activo',
                'imagen' => 'OF 3.jpeg'
            ],
            [
                'espacio_id' => 103,
                'esp_nombre' => 'Aula Magna Digital',
                'esp_descripcion' => 'Espacio amplio equipado con tecnología punta para talleres, capacitaciones y presentaciones.',
                'esp_capacidad' => 25,
                'esp_tipo' => 'Aula',
                'esp_precio_hora' => 120000,
                'esp_estado' => 'Activo',
                'imagen' => 'OF 9.jpeg'
            ],
            [
                'espacio_id' => 104,
                'esp_nombre' => 'Rincón del Inventor',
                'esp_descripcion' => 'Pequeño pero vibrante, perfecto para sesiones de lluvia de ideas y prototipado rápido.',
                'esp_capacidad' => 4,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 25000,
                'esp_estado' => 'Activo',
                'imagen' => 'OF1 .jpeg'
            ],
            [
                'espacio_id' => 105,
                'esp_nombre' => 'Sala Eventos Panorama',
                'esp_descripcion' => 'Vista increíble y gran versatilidad para lanzamientos de productos o eventos de networking.',
                'esp_capacidad' => 50,
                'esp_tipo' => 'Sala de eventos',
                'esp_precio_hora' => 250000,
                'esp_estado' => 'Activo',
                'imagen' => 'OF12.jpeg'
            ],
            [
                'espacio_id' => 106,
                'esp_nombre' => 'Coworking Global',
                'esp_descripcion' => 'Mesas compartidas con internet de alta velocidad y café ilimitado para nómadas digitales.',
                'esp_capacidad' => 14,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 15000,
                'esp_estado' => 'Activo',
                'imagen' => 'Of 14 puestos de trabajo .jpeg'
            ],
            [
                'espacio_id' => 107,
                'esp_nombre' => 'Estudio Creativo Prisma',
                'esp_descripcion' => 'Iluminación natural y mobiliario ergonómico para largas sesiones de diseño y edición.',
                'esp_capacidad' => 5,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 35000,
                'esp_estado' => 'Activo',
                'imagen' => 'Ofic 5 -3.jpeg'
            ],
            [
                'espacio_id' => 108,
                'esp_nombre' => 'Sala Juntas Elite',
                'esp_descripcion' => 'Elegancia y tecnología para cerrar los negocios más importantes en el corazón de la ciudad.',
                'esp_capacidad' => 12,
                'esp_tipo' => 'Sala de reuniones',
                'esp_precio_hora' => 60000,
                'esp_estado' => 'Activo',
                'imagen' => 'Ofic 8.jpeg'
            ],
            [
                'espacio_id' => 109,
                'esp_nombre' => 'Foco de Concentración',
                'esp_descripcion' => 'Aislamiento acústico completo para llamadas importantes o trabajo profundo sin distracciones.',
                'esp_capacidad' => 1,
                'esp_tipo' => 'Oficina',
                'esp_precio_hora' => 20000,
                'esp_estado' => 'Activo',
                'imagen' => 'ofic 11.jpeg'
            ],
            [
                'espacio_id' => 110,
                'esp_nombre' => 'Sala Versátil Beta',
                'esp_descripcion' => 'Configuración flexible que se adapta a cualquier necesidad, desde reuniones hasta workshops.',
                'esp_capacidad' => 10,
                'esp_tipo' => 'Sala de reuniones',
                'esp_precio_hora' => 40000,
                'esp_estado' => 'Activo',
                'imagen' => 'ofic 5 -2.jpeg'
            ],
        ];

        foreach ($espacios as $data) {
            $imagenPath = $data['imagen'];
            unset($data['imagen']);
            
            DB::table('espacios')->insert($data);
            
            DB::table('imagenes')->insert([
                'espacio_id' => $data['espacio_id'],
                'foto' => $imagenPath
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $ids = range(101, 110);
        DB::table('imagenes')->whereIn('espacio_id', $ids)->delete();
        DB::table('espacios')->whereIn('espacio_id', $ids)->delete();
    }
};

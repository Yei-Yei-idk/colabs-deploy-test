<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function create()
    {
        date_default_timezone_set('America/Bogota');

        $fecha = date("Y-m-d_H-i-s");
        $usuario = getenv("USERNAME");

        $backup_dir = "C:\\Users\\$usuario\\Downloads\\";
        $nombre_archivo = "backup_" . $fecha . ".sql";
        $ruta_completa = $backup_dir . $nombre_archivo;

        if (!file_exists($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }

        $servername = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $bd = env('DB_DATABASE');

        $mysqldump_path = "\"C:\\xampp\\mysql\\bin\\mysqldump.exe\"";

        $comando = "$mysqldump_path -h $servername -u $username " .
            (!empty($password) ? "-p$password " : "") .
            "--add-drop-database --databases $bd > \"$ruta_completa\"";

        exec($comando, $output, $resultado);

        if ($resultado === 0) {
            return back()->with('success', 'Copia de seguridad creada exitosamente.');
        } else {
            return back()->with('error', 'Error al crear la copia de seguridad.');
        }
    }

    public function menu()
    {
        return view('admin.copia_seguridad.menu');
    }
}

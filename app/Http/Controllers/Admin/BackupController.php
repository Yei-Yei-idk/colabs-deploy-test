<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function backup()
    {
        $filename = 'backup_'.date('Y-m-d_H-i-s').'.sql';
        $path = storage_path('app/backups/' . $filename);

        //crear carpeta si no existe
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $command = "mysqldump --user=" . env('DB_USERNAME') . 
        " --pasword=" . env('DB_PASSWORD') . 
        " --host=" . env('DB_HOST') . "" . 
        env('DB_DATABASE') . " > $path";

        system($command);

        return response()->download($path);
    }

    public function restore(request $request) 
    {
        $request->validate([
            'backup' => 'required|file|mimes:sql'
        ]);

        $file = $request->file('backup');

        $path = $file->storeAs('backups', $file->getClientOriginalName());

        $fullPath = storage_path('app/' . $path);

        $command = "mysql --user=" . env('DB_USERNAME') . 
        " --password=" . env('DB_PASSWORD') . 
        " --host=" . env("DB_HOST") . "" . 
        env('DB_DATABASE') . " < $fullPath";

        system($command);

        return back()->with('success','Base de datos restaurada correctamente');
    }

    public function menu() {
        return view('admin.copia_seguridad.menu');
    }
}



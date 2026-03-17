    <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insertar roles
        \DB::table('rol')->insertOrIgnore([
            ['rol_id' => 1, 'rol_nombre' => 'Super_admin'],
            ['rol_id' => 2, 'rol_nombre' => 'Admin'],
            ['rol_id' => 3, 'rol_nombre' => 'Usuario'],
        ]);

        // Insertar usuario Super_admin
        \DB::table('usuarios')->insertOrIgnore([
            'user_id' => 1,
            'user_nombre' => 'Super_admin',
            'user_correo' => 'admin@colabs.com',
            'user_telefono' => 3000000000,
            'user_contrasena' => bcrypt('admin123'),
            'rol_id' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('usuarios')->where('user_correo', 'admin@colabs.com')->delete();
        \DB::table('rol')->whereIn('rol_id', [1, 2, 3])->delete();
    }
};

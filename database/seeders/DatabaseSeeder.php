<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'user_nombre' => 'Admin Test',
            'user_correo' => 'admin@admin.com',
            'rol_id' => 1, // Super Admin
            'email_verified_at' => now(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProduccionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermisosSeeder::class,
        ]);

        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'              => 'Administrador Dev',
                'password'          => bcrypt('Admin123'),
                'email_verified_at' => now(),
                'estado'            => 'activo',
            ]
        );

        $superAdmin->syncRoles(['Super Admin']);
    }
}

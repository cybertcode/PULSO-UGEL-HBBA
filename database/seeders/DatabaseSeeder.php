<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UnidadesOrganicasSeeder::class,
            ComponentesSeeder::class,
            ConfiguracionInstitucionalSeeder::class,
            RolesPermisosSeeder::class,
        ]);

        // Usuario administrador por defecto
        $admin = User::firstOrCreate(
            ['email' => 'admin@ugel.gob.pe'],
            [
                'name'              => 'Administrador PULSO UGEL',
                'password'          => Hash::make('Admin@2024'),
                'email_verified_at' => now(),
                'estado'            => 'activo',
            ]
        );
        $admin->assignRole('Administrador');
    }
}

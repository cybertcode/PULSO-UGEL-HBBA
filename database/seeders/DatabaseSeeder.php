<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UnidadesOrganicasSeeder::class,
            ComponentesSeeder::class,
            ConfiguracionInstitucionalSeeder::class,
            RolesPermisosSeeder::class,
            UsuariosSeeder::class,
            DatosSeeder::class,
        ]);
    }
}

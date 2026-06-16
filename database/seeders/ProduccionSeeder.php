<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProduccionSeeder extends Seeder
{
    /**
     * Seeder mínimo para producción: solo catálogos institucionales reales,
     * roles/permisos y el usuario Super Admin. Sin datos ficticios ni de prueba.
     */
    public function run(): void
    {
        $this->call([
            UnidadesOrganicasSeeder::class,
            CargosSeeder::class,
            ComponentesSeeder::class,
            ConfiguracionInstitucionalSeeder::class,
            RolesPermisosSeeder::class,
        ]);

        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'               => 'Administrador Dev',
                'password'           => bcrypt('Admin123'),
                'email_verified_at'  => now(),
                'dni'                => '00000000',
                'cargo_id'           => null,
                'unidad_organica_id' => null,
                'estado'             => 'activo',
            ]
        );

        $superAdmin->syncRoles(['Super Admin']);
    }
}

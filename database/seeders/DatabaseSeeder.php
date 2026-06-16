<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UnidadesOrganicasSeeder::class,
            CargosSeeder::class,
            ComponentesSeeder::class,
            ConfiguracionInstitucionalSeeder::class,
            RolesPermisosSeeder::class,
            UsuariosSeeder::class,
            DatosSeeder::class,
            TrabajadoresDestacadosSeeder::class,
            HistorialRankingSeeder::class,
            DatosComplementariosSeeder::class,
            EstructuraSciIntegridadSeeder::class,
            NuevosModulosSeeder::class,
            BuenasPracticasRecomendacionesSeeder::class,
            AlertasPruebaSeeder::class,
            NormativasSeeder::class,
            EncuestasSeeder::class,
        ]);
    }
}

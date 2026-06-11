<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class EncuestaPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'encuesta.ver',
            'encuesta.crear',
            'encuesta.editar',
            'encuesta.eliminar',
            'encuesta.publicar',
            'encuesta.resultados',
            'encuesta.exportar',
            'encuesta.responder',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $this->command->info('Permisos de encuestas creados correctamente.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UnidadOrganica;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    // Usuarios fijos representativos de una UGEL peruana
    private array $usuariosFijos = [
        [
            'name'   => 'María Elena Quispe Huamán',
            'email'  => 'director@ugel.gob.pe',
            'dni'    => '42185634',
            'cargo'  => 'Directora de UGEL',
            'unidad' => 'DIR',
            'rol'    => 'Administrador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Carlos Alberto Flores Mendoza',
            'email'  => 'sci@ugel.gob.pe',
            'dni'    => '43297851',
            'cargo'  => 'Coordinador de Control Interno',
            'unidad' => 'AGI',
            'rol'    => 'Administrador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Rosa Isabel Vargas Tarazona',
            'email'  => 'administracion@ugel.gob.pe',
            'dni'    => '44512378',
            'cargo'  => 'Jefa de Área de Administración',
            'unidad' => 'OAD',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Jorge Luis Ramírez Castillo',
            'email'  => 'pedagogia@ugel.gob.pe',
            'dni'    => '45623894',
            'cargo'  => 'Jefe de Área de Gestión Pedagógica',
            'unidad' => 'AGP',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Ana Lucía Torres Espinoza',
            'email'  => 'contabilidad@ugel.gob.pe',
            'dni'    => '46734521',
            'cargo'  => 'Responsable de Contabilidad',
            'unidad' => 'CONT',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Pedro Antonio Huanca Mamani',
            'email'  => 'logistica@ugel.gob.pe',
            'dni'    => '47845632',
            'cargo'  => 'Responsable de Logística',
            'unidad' => 'LOG',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Lucía Fernández Ríos',
            'email'  => 'rrhh@ugel.gob.pe',
            'dni'    => '48956743',
            'cargo'  => 'Responsable de Recursos Humanos',
            'unidad' => 'RR_HH',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Juan Carlos Soto Benites',
            'email'  => 'tesoreria@ugel.gob.pe',
            'dni'    => '49067854',
            'cargo'  => 'Responsable de Tesorería',
            'unidad' => 'TESOR',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Sandra Milagros León Coronado',
            'email'  => 'infraestructura@ugel.gob.pe',
            'dni'    => '40178965',
            'cargo'  => 'Responsable de Infraestructura',
            'unidad' => 'INF',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Roberto Enrique Chávez Palacios',
            'email'  => 'asesor@ugel.gob.pe',
            'dni'    => '41289076',
            'cargo'  => 'Asesor Legal',
            'unidad' => 'ASESOR',
            'rol'    => 'Visualizador',
            'estado' => 'activo',
        ],
    ];

    public function run(): void
    {
        // Usuario de acceso rápido para desarrollo
        $devAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'              => 'Administrador',
                'password'          => \Illuminate\Support\Facades\Hash::make('Admin123'),
                'email_verified_at' => now(),
                'dni'               => '00000000',
                'cargo'             => 'Administrador del Sistema',
                'estado'            => 'activo',
            ]
        );
        $devAdmin->syncRoles(['Super Admin']);

        foreach ($this->usuariosFijos as $datos) {
            $unidad = UnidadOrganica::where('codigo', $datos['unidad'])->first();

            $user = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name'               => $datos['name'],
                    'password'           => \Illuminate\Support\Facades\Hash::make('Ugel@2024'),
                    'email_verified_at'  => now(),
                    'dni'                => $datos['dni'],
                    'cargo'              => $datos['cargo'],
                    'unidad_organica_id' => $unidad?->id,
                    'estado'             => $datos['estado'],
                ]
            );

            $user->syncRoles([$datos['rol']]);
        }

        // Usuarios adicionales con factory (faker es_PE)
        $roles = ['Operador', 'Operador', 'Visualizador', 'Visualizador', 'Responsable de Unidad'];

        User::factory(15)->activo()->create()->each(function ($user) use ($roles) {
            $user->assignRole(fake()->randomElement($roles));
        });

        // 2 usuarios pendientes de verificación
        User::factory(2)->unverified()->create()->each(function ($user) {
            $user->assignRole('Visualizador');
        });
    }
}

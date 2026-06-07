<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UnidadOrganica;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Usuarios fijos representativos de una UGEL peruana.
     * Clave 'rol' corresponde exactamente a los nombres en RolesPermisosSeeder.
     */
    private array $usuariosFijos = [
        // ── Dirección ─────────────────────────────────────────────────────────
        [
            'name'   => 'María Elena Quispe Huamán',
            'email'  => 'director@ugel.gob.pe',
            'dni'    => '42185634',
            'cargo'  => 'Directora de UGEL',
            'unidad' => 'DIR',
            'rol'    => 'Administrador',
            'estado' => 'activo',
        ],
        // ── Coordinación SCI ──────────────────────────────────────────────────
        [
            'name'   => 'Carlos Alberto Flores Mendoza',
            'email'  => 'sci@ugel.gob.pe',
            'dni'    => '43297851',
            'cargo'  => 'Coordinador de Control Interno',
            'unidad' => 'AGI',
            'rol'    => 'Coordinador SCI',
            'estado' => 'activo',
        ],
        // ── Administración ────────────────────────────────────────────────────
        [
            'name'   => 'Rosa Isabel Vargas Tarazona',
            'email'  => 'administracion@ugel.gob.pe',
            'dni'    => '44512378',
            'cargo'  => 'Jefa de Oficina de Administración',
            'unidad' => 'OAD',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        // ── Área de Gestión Pedagógica ────────────────────────────────────────
        [
            'name'   => 'Jorge Luis Ramírez Castillo',
            'email'  => 'pedagogia@ugel.gob.pe',
            'dni'    => '45623894',
            'cargo'  => 'Jefe de Área de Gestión Pedagógica',
            'unidad' => 'AGP',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        // ── Contabilidad ──────────────────────────────────────────────────────
        [
            'name'   => 'Ana Lucía Torres Espinoza',
            'email'  => 'contabilidad@ugel.gob.pe',
            'dni'    => '46734521',
            'cargo'  => 'Responsable de Contabilidad',
            'unidad' => 'CONT',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        // ── Logística ─────────────────────────────────────────────────────────
        [
            'name'   => 'Pedro Antonio Huanca Mamani',
            'email'  => 'logistica@ugel.gob.pe',
            'dni'    => '47845632',
            'cargo'  => 'Responsable de Logística',
            'unidad' => 'LOG',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Recursos Humanos ──────────────────────────────────────────────────
        [
            'name'   => 'Lucía Fernández Ríos',
            'email'  => 'rrhh@ugel.gob.pe',
            'dni'    => '48956743',
            'cargo'  => 'Responsable de Recursos Humanos',
            'unidad' => 'RR_HH',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Tesorería ─────────────────────────────────────────────────────────
        [
            'name'   => 'Juan Carlos Soto Benites',
            'email'  => 'tesoreria@ugel.gob.pe',
            'dni'    => '49067854',
            'cargo'  => 'Responsable de Tesorería',
            'unidad' => 'TESOR',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Infraestructura ───────────────────────────────────────────────────
        [
            'name'   => 'Sandra Milagros León Coronado',
            'email'  => 'infraestructura@ugel.gob.pe',
            'dni'    => '40178965',
            'cargo'  => 'Responsable de Infraestructura',
            'unidad' => 'INF',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Asesoría Jurídica ─────────────────────────────────────────────────
        [
            'name'   => 'Roberto Enrique Chávez Palacios',
            'email'  => 'asesor@ugel.gob.pe',
            'dni'    => '41289076',
            'cargo'  => 'Asesor Jurídico',
            'unidad' => 'ASESOR',
            'rol'    => 'Visualizador',
            'estado' => 'activo',
        ],
        // ── Especialistas adicionales ─────────────────────────────────────────
        [
            'name'   => 'Patricia Soledad Mejía Sánchez',
            'email'  => 'especialista.agi@ugel.gob.pe',
            'dni'    => '47123456',
            'cargo'  => 'Especialista en Gestión Institucional',
            'unidad' => 'AGI',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Marco Antonio Príncipe López',
            'email'  => 'especialista.agp@ugel.gob.pe',
            'dni'    => '44512367',
            'cargo'  => 'Especialista en Gestión Pedagógica',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Sofía Alejandra Vega Castillo',
            'email'  => 'especialista.inf@ugel.gob.pe',
            'dni'    => '48234567',
            'cargo'  => 'Especialista en Infraestructura',
            'unidad' => 'INF',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Luis Alberto Quispe Mamani',
            'email'  => 'contador@ugel.gob.pe',
            'dni'    => '43125698',
            'cargo'  => 'Contador Público',
            'unidad' => 'CONT',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Visualizadores institucionales ────────────────────────────────────
        [
            'name'   => 'Karina Beatriz Huanca Quispe',
            'email'  => 'monitor@ugel.gob.pe',
            'dni'    => '46321987',
            'cargo'  => 'Monitora de Integridad',
            'unidad' => 'AGI',
            'rol'    => 'Visualizador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Fernando José Ramos Delgado',
            'email'  => 'secretaria@ugel.gob.pe',
            'dni'    => '45789231',
            'cargo'  => 'Secretario de Dirección',
            'unidad' => 'DIR',
            'rol'    => 'Visualizador',
            'estado' => 'activo',
        ],
    ];

    public function run(): void
    {
        // ── Super Admin de desarrollo ──────────────────────────────────────────
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

        // ── Usuarios fijos institucionales ─────────────────────────────────────
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

        // ── Usuarios faker adicionales ─────────────────────────────────────────
        // Distribución realista de roles para una institución educativa
        $rolesAdicionales = [
            'Operador'             => 10,
            'Visualizador'         => 8,
            'Responsable de Unidad'=> 3,
            'Coordinador SCI'      => 1,
        ];

        foreach ($rolesAdicionales as $rol => $cantidad) {
            User::factory($cantidad)->activo()->create()->each(
                fn($user) => $user->syncRoles([$rol])
            );
        }

        // ── 2 usuarios inactivos/pendientes para pruebas de estado ────────────
        User::factory(2)->unverified()->create()->each(
            fn($user) => $user->syncRoles(['Visualizador'])
        );

        // ── 1 usuario suspendido ───────────────────────────────────────────────
        $suspendido = User::factory()->create([
            'estado' => 'inactivo',
            'email_verified_at' => now(),
        ]);
        $suspendido->syncRoles(['Operador']);
    }
}

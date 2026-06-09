<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\User;
use App\Models\UnidadOrganica;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Usuarios representativos de la UGEL Huacaybamba — Huánuco, Perú.
     * Clave 'rol' corresponde exactamente a los nombres en RolesPermisosSeeder.
     */
    private array $usuariosFijos = [
        // ── Dirección ─────────────────────────────────────────────────────────
        [
            'name'   => 'Mg. Julio Luis Lozano Yllatopa',
            'email'  => 'director@ugelhuacaybamba.edu.pe',
            'dni'    => '42185634',
            'cargo'  => 'Director(a) de UGEL',
            'unidad' => 'DIR',
            'rol'    => 'Administrador',
            'estado' => 'activo',
        ],
        // ── Coordinación SCI ──────────────────────────────────────────────────
        [
            'name'   => 'Carlos Alberto Flores Mendoza',
            'email'  => 'sci@ugelhuacaybamba.edu.pe',
            'dni'    => '43297851',
            'cargo'  => 'Coordinador(a) de Control Interno',
            'unidad' => 'AGI',
            'rol'    => 'Coordinador SCI',
            'estado' => 'activo',
        ],
        // ── Gestión Administrativa ────────────────────────────────────────────
        [
            'name'   => 'Rosa Isabel Vargas Tarazona',
            'email'  => 'administracion@ugelhuacaybamba.edu.pe',
            'dni'    => '44512378',
            'cargo'  => 'Jefe(a) de Oficina de Administración',
            'unidad' => 'OAD',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        // ── Área de Gestión Pedagógica ────────────────────────────────────────
        [
            'name'   => 'Jorge Luis Ramírez Castillo',
            'email'  => 'pedagogia@ugelhuacaybamba.edu.pe',
            'dni'    => '45623894',
            'cargo'  => 'Jefe(a) de Área de Gestión Pedagógica',
            'unidad' => 'AGP',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        // ── Contabilidad ──────────────────────────────────────────────────────
        [
            'name'   => 'Ana Lucía Torres Espinoza',
            'email'  => 'contabilidad@ugelhuacaybamba.edu.pe',
            'dni'    => '46734521',
            'cargo'  => 'Responsable de Contabilidad',
            'unidad' => 'CONT',
            'rol'    => 'Responsable de Unidad',
            'estado' => 'activo',
        ],
        // ── Logística ─────────────────────────────────────────────────────────
        [
            'name'   => 'Pedro Antonio Huanca Mamani',
            'email'  => 'logistica@ugelhuacaybamba.edu.pe',
            'dni'    => '47845632',
            'cargo'  => 'Responsable de Logística',
            'unidad' => 'LOG',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Recursos Humanos ──────────────────────────────────────────────────
        [
            'name'   => 'Lucía Fernández Ríos',
            'email'  => 'rrhh@ugelhuacaybamba.edu.pe',
            'dni'    => '48956743',
            'cargo'  => 'Responsable de Recursos Humanos',
            'unidad' => 'RR_HH',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Tesorería ─────────────────────────────────────────────────────────
        [
            'name'   => 'Juan Carlos Soto Benites',
            'email'  => 'tesoreria@ugelhuacaybamba.edu.pe',
            'dni'    => '49067854',
            'cargo'  => 'Responsable de Tesorería',
            'unidad' => 'TESOR',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Infraestructura ───────────────────────────────────────────────────
        [
            'name'   => 'Sandra Milagros León Coronado',
            'email'  => 'infraestructura@ugelhuacaybamba.edu.pe',
            'dni'    => '40178965',
            'cargo'  => 'Responsable de Infraestructura',
            'unidad' => 'INF',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Asesoría Jurídica ─────────────────────────────────────────────────
        [
            'name'   => 'Roberto Enrique Chávez Palacios',
            'email'  => 'asesoria@ugelhuacaybamba.edu.pe',
            'dni'    => '41289076',
            'cargo'  => 'Asesor(a) Jurídico(a)',
            'unidad' => 'ASESOR',
            'rol'    => 'Visualizador',
            'estado' => 'activo',
        ],
        // ── Especialistas ─────────────────────────────────────────────────────
        [
            'name'   => 'Patricia Soledad Mejía Sánchez',
            'email'  => 'especialista.agi@ugelhuacaybamba.edu.pe',
            'dni'    => '47123456',
            'cargo'  => 'Especialista en Gestión Institucional',
            'unidad' => 'AGI',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Marco Antonio Príncipe López',
            'email'  => 'especialista.agp@ugelhuacaybamba.edu.pe',
            'dni'    => '44512367',
            'cargo'  => 'Especialista en Gestión Pedagógica',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Sofía Alejandra Vega Castillo',
            'email'  => 'especialista.inf@ugelhuacaybamba.edu.pe',
            'dni'    => '48234567',
            'cargo'  => 'Especialista en Infraestructura',
            'unidad' => 'INF',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Luis Alberto Quispe Mamani',
            'email'  => 'contador@ugelhuacaybamba.edu.pe',
            'dni'    => '43125698',
            'cargo'  => 'Contador Público',
            'unidad' => 'CONT',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        // ── Visualizadores institucionales ────────────────────────────────────
        [
            'name'   => 'Karina Beatriz Huanca Quispe',
            'email'  => 'monitor@ugelhuacaybamba.edu.pe',
            'dni'    => '46321987',
            'cargo'  => 'Monitora de Integridad',
            'unidad' => 'AGI',
            'rol'    => 'Visualizador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Fernando José Ramos Delgado',
            'email'  => 'secretaria@ugelhuacaybamba.edu.pe',
            'dni'    => '45789231',
            'cargo'  => 'Secretario(a) de Dirección',
            'unidad' => 'DIR',
            'rol'    => 'Visualizador',
            'estado' => 'activo',
        ],
        // ── Especialistas pedagógicos adicionales (zona rural) ────────────────
        [
            'name'   => 'Yolanda Esperanza Condori Huanca',
            'email'  => 'especialista.inicial@ugelhuacaybamba.edu.pe',
            'dni'    => '44398712',
            'cargo'  => 'Especialista en Educación Inicial',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Raúl Ernesto Meza Tucto',
            'email'  => 'especialista.primaria@ugelhuacaybamba.edu.pe',
            'dni'    => '46587234',
            'cargo'  => 'Especialista en Educación Primaria',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
        [
            'name'   => 'Mirtha Jacqueline Soto Villanueva',
            'email'  => 'especialista.secundaria@ugelhuacaybamba.edu.pe',
            'dni'    => '47891234',
            'cargo'  => 'Especialista en Educación Secundaria',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
            'estado' => 'activo',
        ],
    ];

    public function run(): void
    {
        $cargoMap = Cargo::pluck('id', 'nombre');

        // ── Super Admin de desarrollo ──────────────────────────────────────────
        $devAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'              => 'Administrador',
                'password'          => \Illuminate\Support\Facades\Hash::make('Admin123'),
                'email_verified_at' => now(),
                'dni'               => '00000000',
                'cargo_id'          => null,
                'estado'            => 'activo',
            ]
        );
        $devAdmin->syncRoles(['Super Admin']);

        // ── Usuarios fijos institucionales ─────────────────────────────────────
        foreach ($this->usuariosFijos as $datos) {
            $unidad  = UnidadOrganica::where('codigo', $datos['unidad'])->first();
            $cargoId = $cargoMap->get($datos['cargo']);

            $user = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name'               => $datos['name'],
                    'password'           => \Illuminate\Support\Facades\Hash::make('Ugel@2024'),
                    'email_verified_at'  => now(),
                    'dni'                => $datos['dni'],
                    'cargo_id'           => $cargoId,
                    'unidad_organica_id' => $unidad?->id,
                    'estado'             => $datos['estado'],
                ]
            );

            $user->syncRoles([$datos['rol']]);
        }

        // ── Usuarios faker adicionales ─────────────────────────────────────────
        $rolesAdicionales = [
            'Operador'              => 8,
            'Visualizador'          => 6,
            'Responsable de Unidad' => 2,
            'Coordinador SCI'       => 1,
        ];

        foreach ($rolesAdicionales as $rol => $cantidad) {
            User::factory($cantidad)->activo()->create()->each(
                fn($user) => $user->syncRoles([$rol])
            );
        }

        // ── 2 usuarios pendientes de verificación ─────────────────────────────
        User::factory(2)->unverified()->create()->each(
            fn($user) => $user->syncRoles(['Visualizador'])
        );

        // ── 1 usuario inactivo ─────────────────────────────────────────────────
        $suspendido = User::factory()->create([
            'estado' => 'inactivo',
            'email_verified_at' => now(),
        ]);
        $suspendido->syncRoles(['Operador']);
    }
}

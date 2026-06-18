<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\User;
use App\Models\UnidadOrganica;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Usuarios representativos de la UGEL Huacaybamba — Huánuco, Perú.
     * Contraseña institucional: Ugel@2024 (configurable con SEED_USERS_PASSWORD en .env)
     * Contraseña dev (Super Admin): Admin123
     *
     * Roles disponibles (en orden de privilegio descendente):
     *   Super Admin > Administrador > Coordinador SCI > Responsable de Unidad > Operador > Visualizador
     */
    private array $usuariosFijos = [

        // ── Dirección General ────────────────────────────────────────────────
        [
            'name'   => 'Mg. Julio Luis Lozano Yllatopa',
            'email'  => 'director@ugelhuacaybamba.edu.pe',
            'dni'    => '42185634',
            'cargo'  => 'Director(a) de UGEL',
            'unidad' => 'DIR',
            'rol'    => 'Administrador',
            // Acceso: gestión total institucional, usuarios, configuración, todos los módulos.
            // NO puede gestionar roles del sistema (solo Super Admin).
        ],

        // ── Coordinación de Control Interno (SCI) ────────────────────────────
        [
            'name'   => 'Carlos Alberto Flores Mendoza',
            'email'  => 'sci@ugelhuacaybamba.edu.pe',
            'dni'    => '43297851',
            'cargo'  => 'Coordinador(a) de Control Interno',
            'unidad' => 'AGI',
            'rol'    => 'Coordinador SCI',
            // Acceso: gestión completa SCI e Integridad en toda la institución.
            // NO gestiona usuarios ni configuración del sistema.
        ],

        // ── Jefes de Área (Responsables de Unidad) ──────────────────────────
        [
            'name'   => 'Rosa Isabel Vargas Tarazona',
            'email'  => 'administracion@ugelhuacaybamba.edu.pe',
            'dni'    => '44512378',
            'cargo'  => 'Jefe(a) de Oficina de Administración',
            'unidad' => 'OAD',
            'rol'    => 'Responsable de Unidad',
            // Acceso: crea y edita actividades de su unidad. No elimina.
        ],
        [
            'name'   => 'Jorge Luis Ramírez Castillo',
            'email'  => 'pedagogia@ugelhuacaybamba.edu.pe',
            'dni'    => '45623894',
            'cargo'  => 'Jefe(a) de Área de Gestión Pedagógica',
            'unidad' => 'AGP',
            'rol'    => 'Responsable de Unidad',
        ],
        [
            'name'   => 'Ana Lucía Torres Espinoza',
            'email'  => 'contabilidad@ugelhuacaybamba.edu.pe',
            'dni'    => '46734521',
            'cargo'  => 'Responsable de Contabilidad',
            'unidad' => 'CONT',
            'rol'    => 'Responsable de Unidad',
        ],

        // ── Operadores (actualizan avances y suben evidencias) ────────────────
        [
            'name'   => 'Pedro Antonio Huanca Mamani',
            'email'  => 'logistica@ugelhuacaybamba.edu.pe',
            'dni'    => '47845632',
            'cargo'  => 'Responsable de Logística',
            'unidad' => 'LOG',
            'rol'    => 'Operador',
            // Acceso: edita avances en actividades asignadas, sube evidencias.
            // NO crea actividades ni gestiona alertas.
        ],
        [
            'name'   => 'Lucía Fernández Ríos',
            'email'  => 'rrhh@ugelhuacaybamba.edu.pe',
            'dni'    => '48956743',
            'cargo'  => 'Responsable de Recursos Humanos',
            'unidad' => 'RR_HH',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Juan Carlos Soto Benites',
            'email'  => 'tesoreria@ugelhuacaybamba.edu.pe',
            'dni'    => '49067854',
            'cargo'  => 'Responsable de Tesorería',
            'unidad' => 'TESOR',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Sandra Milagros León Coronado',
            'email'  => 'infraestructura@ugelhuacaybamba.edu.pe',
            'dni'    => '40178965',
            'cargo'  => 'Responsable de Infraestructura',
            'unidad' => 'INF',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Patricia Soledad Mejía Sánchez',
            'email'  => 'especialista.agi@ugelhuacaybamba.edu.pe',
            'dni'    => '47123456',
            'cargo'  => 'Especialista en Gestión Institucional',
            'unidad' => 'AGI',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Marco Antonio Príncipe López',
            'email'  => 'especialista.agp@ugelhuacaybamba.edu.pe',
            'dni'    => '44512367',
            'cargo'  => 'Especialista en Gestión Pedagógica',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Sofía Alejandra Vega Castillo',
            'email'  => 'especialista.inf@ugelhuacaybamba.edu.pe',
            'dni'    => '48234567',
            'cargo'  => 'Especialista en Infraestructura',
            'unidad' => 'INF',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Luis Alberto Quispe Mamani',
            'email'  => 'contador@ugelhuacaybamba.edu.pe',
            'dni'    => '43125698',
            'cargo'  => 'Contador Público',
            'unidad' => 'CONT',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Yolanda Esperanza Condori Huanca',
            'email'  => 'especialista.inicial@ugelhuacaybamba.edu.pe',
            'dni'    => '44398712',
            'cargo'  => 'Especialista en Educación Inicial',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Raúl Ernesto Meza Tucto',
            'email'  => 'especialista.primaria@ugelhuacaybamba.edu.pe',
            'dni'    => '46587234',
            'cargo'  => 'Especialista en Educación Primaria',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
        ],
        [
            'name'   => 'Mirtha Jacqueline Soto Villanueva',
            'email'  => 'especialista.secundaria@ugelhuacaybamba.edu.pe',
            'dni'    => '47891234',
            'cargo'  => 'Especialista en Educación Secundaria',
            'unidad' => 'AGP',
            'rol'    => 'Operador',
        ],

        // ── Visualizadores (solo lectura total) ──────────────────────────────
        [
            'name'   => 'Roberto Enrique Chávez Palacios',
            'email'  => 'asesoria@ugelhuacaybamba.edu.pe',
            'dni'    => '41289076',
            'cargo'  => 'Asesor(a) Jurídico(a)',
            'unidad' => 'ASESOR',
            'rol'    => 'Visualizador',
            // Acceso: solo lectura total. Puede ver cumplimiento y reconocimientos
            // (a diferencia del Operador que no ve cumplimiento).
        ],
        [
            'name'   => 'Karina Beatriz Huanca Quispe',
            'email'  => 'monitor@ugelhuacaybamba.edu.pe',
            'dni'    => '46321987',
            'cargo'  => 'Monitora de Integridad',
            'unidad' => 'AGI',
            'rol'    => 'Visualizador',
        ],
        [
            'name'   => 'Fernando José Ramos Delgado',
            'email'  => 'secretaria@ugelhuacaybamba.edu.pe',
            'dni'    => '45789231',
            'cargo'  => 'Secretario(a) de Dirección',
            'unidad' => 'DIR',
            'rol'    => 'Visualizador',
        ],
    ];

    public function run(): void
    {
        $cargoMap = Cargo::pluck('id', 'nombre');
        $password = env('SEED_USERS_PASSWORD', 'Ugel@2024');

        // ── 1. Super Admin de desarrollo ──────────────────────────────────────
        $devAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'              => 'Administrador Dev',
                'password'          => Hash::make('Admin123'),
                'email_verified_at' => now(),
                'dni'               => '00000000',
                'unidad_organica_id'=> null,
                'estado'            => 'activo',
            ]
        );
        $devAdmin->syncRoles(['Super Admin']);

        // ── 2. Usuarios institucionales fijos ─────────────────────────────────
        foreach ($this->usuariosFijos as $datos) {
            $unidad  = UnidadOrganica::where('codigo', $datos['unidad'])->first();
            $cargoId = $cargoMap->get($datos['cargo']);

            $user = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name'               => $datos['name'],
                    'password'           => Hash::make($password),
                    'email_verified_at'  => now(),
                    'dni'                => $datos['dni'],
                    'unidad_organica_id' => $unidad?->id,
                    'estado'             => 'activo',
                ]
            );
            if ($cargoId) {
                $user->cargos()->syncWithoutDetaching([$cargoId]);
            }
            $user->syncRoles([$datos['rol']]);
        }

        // ── 3. Usuarios faker adicionales para poblar métricas ────────────────
        $extras = [
            'Operador'              => 8,
            'Visualizador'          => 5,
            'Responsable de Unidad' => 3,
            'Coordinador SCI'       => 1,
        ];

        foreach ($extras as $rol => $cantidad) {
            User::factory($cantidad)->activo()->create()->each(
                fn($u) => $u->syncRoles([$rol])
            );
        }

        // ── 4. Casos especiales para probar flujos de autenticación ───────────
        // 2 usuarios sin verificar email
        User::factory(2)->unverified()->create()->each(
            fn($u) => $u->syncRoles(['Visualizador'])
        );

        // 1 usuario inactivo (login bloqueado)
        User::factory()->create(['estado' => 'inactivo', 'email_verified_at' => now()])
            ->syncRoles(['Operador']);

        // 1 usuario pendiente de activación
        User::factory()->create(['estado' => 'pendiente', 'email_verified_at' => now()])
            ->syncRoles(['Operador']);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Evidencia;
use App\Models\User;
use App\Notifications\ActividadAsignada;
use App\Notifications\AvanceActualizado;
use App\Notifications\EvidenciaEnviada;
use Illuminate\Database\Seeder;

/**
 * Seeder para probar el sistema de notificaciones implementado.
 * Ejecutar: php artisan db:seed --class=NotificacionesPruebaSeeder
 */
class NotificacionesPruebaSeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('notifications')->truncate();
        $this->command->info('Notificaciones anteriores eliminadas.');

        // ── Usuarios de referencia ────────────────────────────────────────────
        $admin    = User::where('email', 'admin@admin.com')->first();
        $director = User::where('email', 'director@ugelhuacaybamba.edu.pe')->first();
        $sci      = User::where('email', 'sci@ugelhuacaybamba.edu.pe')->first();
        $jefe     = User::where('email', 'administracion@ugelhuacaybamba.edu.pe')->first();
        $operador = User::where('email', 'logistica@ugelhuacaybamba.edu.pe')->first();
        $operador2 = User::where('email', 'rrhh@ugelhuacaybamba.edu.pe')->first();

        if (!$admin || !$director || !$sci || !$jefe || !$operador) {
            $this->command->error('Faltan usuarios base. Ejecuta primero UsuariosSeeder.');
            return;
        }

        // ── Obtener actividades existentes ────────────────────────────────────
        $actividades = Actividad::with('responsables')->take(10)->get();

        if ($actividades->isEmpty()) {
            $this->command->error('No hay actividades. Ejecuta DatosSeeder primero.');
            return;
        }

        // Asegurar que haya al menos una actividad con supervisor (director)
        $actBase = $actividades->first();
        $this->asegurarResponsable($actBase, $operador->id, 'principal');
        $this->asegurarResponsable($actBase, $director->id, 'supervisor');
        $actBase->load('responsables');

        $actJefe = $actividades->count() > 1 ? $actividades->get(1) : $actBase;
        $this->asegurarResponsable($actJefe, $jefe->id, 'principal');
        $actJefe->load('responsables');

        // ── A: Nueva actividad asignada → operador y operador2 ───────────────
        $this->command->line("→ [A] ActividadAsignada (nueva, principal) → {$operador->name}");
        $operador->notify(new ActividadAsignada($actBase, 'nueva', 'principal'));

        if ($operador2) {
            $this->command->line("→ [A] ActividadAsignada (nueva, colaborador) → {$operador2->name}");
            $operador2->notify(new ActividadAsignada($actBase, 'nueva', 'colaborador'));
        }

        // ── B: Fecha límite cambiada → jefe ──────────────────────────────────
        $this->command->line("→ [B] ActividadAsignada (fecha_limite) → {$jefe->name}");
        $jefe->notify(new ActividadAsignada($actJefe, 'fecha_limite', 'principal'));

        // ── C: Avance actualizado → director (supervisor) ────────────────────
        $this->command->line("→ [C] AvanceActualizado (40%→70%) → {$director->name}");
        $director->notify(new AvanceActualizado($actBase, 70, 40, $operador->name));

        ActividadHistorial::create([
            'actividad_id'   => $actBase->id,
            'usuario_id'     => $operador->id,
            'campo'          => 'avance',
            'valor_anterior' => '40%',
            'valor_nuevo'    => '70%',
            'descripcion'    => 'Avance actualizado de 40% a 70% — prueba seeder',
        ]);

        $this->command->line("→ [C] AvanceActualizado (70%→90%) → {$director->name}");
        $director->notify(new AvanceActualizado($actBase, 90, 70, $operador->name));

        ActividadHistorial::create([
            'actividad_id'   => $actBase->id,
            'usuario_id'     => $operador->id,
            'campo'          => 'avance',
            'valor_anterior' => '70%',
            'valor_nuevo'    => '90%',
            'descripcion'    => 'Avance actualizado de 70% a 90% — prueba seeder',
        ]);

        // ── D: Evidencia enviada → validadores (sci y admin) ─────────────────
        $evidencia = Evidencia::with('actividad')->first();

        if ($evidencia) {
            $this->command->line("→ [D] EvidenciaEnviada (nueva) → {$sci->name}");
            $sci->notify(new EvidenciaEnviada($evidencia, 'nueva'));

            $this->command->line("→ [D] EvidenciaEnviada (nueva) → {$admin->name}");
            $admin->notify(new EvidenciaEnviada($evidencia, 'nueva'));

            // ── E: Evidencia corregida tras rechazo ──────────────────────────
            $this->command->line("→ [E] EvidenciaEnviada (corregida) → {$sci->name}");
            $sci->notify(new EvidenciaEnviada($evidencia, 'corregida'));
        } else {
            $this->command->warn('No hay evidencias. Los escenarios D y E se omiten.');
        }

        // ── F: Múltiples notifs para admin (demo campana llena) ──────────────
        $this->command->line("→ [F] ActividadAsignada (nueva, supervisor) → {$admin->name}");
        $admin->notify(new ActividadAsignada($actBase, 'nueva', 'supervisor'));

        $this->command->line("→ [F] AvanceActualizado (30%→55%) → {$admin->name}");
        $admin->notify(new AvanceActualizado($actJefe, 55, 30, $jefe->name));

        // ── Resumen ───────────────────────────────────────────────────────────
        $total = \DB::table('notifications')->count();
        $this->command->newLine();
        $this->command->info("✓ {$total} notificaciones creadas en la BD.");
        $this->command->newLine();

        $resumen = [
            ['admin@admin.com',                       'Admin123',  'Super Admin — ve todas las notifs'],
            ['director@ugelhuacaybamba.edu.pe',        'Ugel@2024', 'Supervisor — ve avances de operador'],
            ['sci@ugelhuacaybamba.edu.pe',             'Ugel@2024', 'Validador — ve evidencias nuevas/corregidas'],
            ['logistica@ugelhuacaybamba.edu.pe',       'Ugel@2024', 'Operador — ve asignación nueva'],
            ['administracion@ugelhuacaybamba.edu.pe',  'Ugel@2024', 'Jefe — ve cambio de fecha límite'],
        ];

        $this->command->table(['Email', 'Contraseña', 'Qué verá en la campana'], $resumen);
    }

    private function asegurarResponsable(Actividad $actividad, int $userId, string $tipo): void
    {
        $existe = \DB::table('actividad_responsables')
            ->where('actividad_id', $actividad->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$existe) {
            \DB::table('actividad_responsables')->insert([
                'actividad_id' => $actividad->id,
                'user_id'      => $userId,
                'tipo'         => $tipo,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}

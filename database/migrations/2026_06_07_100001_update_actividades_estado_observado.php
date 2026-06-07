<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar 'cancelada' por 'observado' en el enum de estado
        DB::statement("ALTER TABLE actividades MODIFY COLUMN estado ENUM('pendiente','en_proceso','completada','observado','vencida') DEFAULT 'pendiente'");

        // Actualizar registros que tenían 'cancelada' al nuevo estado más cercano
        DB::statement("UPDATE actividades SET estado = 'observado' WHERE estado = 'cancelada'");
    }

    public function down(): void
    {
        DB::statement("UPDATE actividades SET estado = 'cancelada' WHERE estado = 'observado'");
        DB::statement("ALTER TABLE actividades MODIFY COLUMN estado ENUM('pendiente','en_proceso','completada','vencida','cancelada') DEFAULT 'pendiente'");
    }
};

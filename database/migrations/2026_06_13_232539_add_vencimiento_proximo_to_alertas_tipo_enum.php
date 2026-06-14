<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE alertas MODIFY COLUMN tipo ENUM(
            'vencimiento',
            'vencimiento_proximo',
            'avance_bajo',
            'evidencia_falta',
            'sistema'
        ) NOT NULL DEFAULT 'sistema'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE alertas MODIFY COLUMN tipo ENUM(
            'vencimiento',
            'avance_bajo',
            'evidencia_falta',
            'sistema'
        ) NOT NULL DEFAULT 'sistema'");
    }
};

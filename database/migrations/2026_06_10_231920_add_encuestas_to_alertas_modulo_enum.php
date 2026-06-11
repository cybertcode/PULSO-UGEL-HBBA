<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE alertas MODIFY COLUMN modulo ENUM('sci','integridad','encuestas') NOT NULL");
    }

    public function down(): void
    {
        DB::table('alertas')->where('modulo', 'encuestas')->delete();
        DB::statement("ALTER TABLE alertas MODIFY COLUMN modulo ENUM('sci','integridad') NOT NULL");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_institucional', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_institucion')->default('UGEL Huacaybamba');
            $table->string('sigla', 30)->default('UGEL-HCB');
            $table->string('ugel_codigo', 20)->nullable();
            $table->string('region', 100)->default('Huánuco');
            $table->string('provincia', 100)->default('Huacaybamba');
            $table->string('director')->nullable();
            $table->string('coordinador_sci')->nullable();
            $table->string('correo_institucional')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('logo_ruta', 500)->nullable();
            $table->year('anio_gestion')->nullable();
            // Umbrales del semáforo (%)
            $table->unsignedTinyInteger('umbral_verde')->default(75);
            $table->unsignedTinyInteger('umbral_amarillo')->default(50);
            // Notificaciones
            $table->boolean('notif_vencimiento')->default(true);
            $table->unsignedTinyInteger('notif_dias_anticipacion')->default(7);
            $table->boolean('notif_avance_bajo')->default(true);
            $table->unsignedTinyInteger('notif_umbral_avance')->default(30);
            $table->boolean('notif_email')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_institucional');
    }
};

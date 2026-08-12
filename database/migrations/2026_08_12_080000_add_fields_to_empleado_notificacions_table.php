<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('empleado_notificacions', function (Blueprint $table) {
            $table->string('tipo')->change();
            $table->string('titulo')->nullable()->change();
            $table->text('contenido')->nullable()->change();
            $table->date('fecha_comunicacion')->nullable()->after('contenido');
            $table->date('fecha_efecto')->nullable()->after('fecha_comunicacion');
            $table->string('gravedad')->nullable()->after('fecha_efecto');
            $table->string('resolucion_cierre')->nullable()->after('gravedad');
            $table->integer('dias_suspension')->nullable()->after('resolucion_cierre');
            $table->string('file_path')->nullable()->after('dias_suspension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_notificacions', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_comunicacion',
                'fecha_efecto',
                'gravedad',
                'resolucion_cierre',
                'dias_suspension',
                'file_path',
            ]);
        });
    }
};

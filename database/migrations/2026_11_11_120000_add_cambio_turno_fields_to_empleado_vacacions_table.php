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
        Schema::table('empleado_vacacions', function (Blueprint $table) {
            if (!Schema::hasColumn('empleado_vacacions', 'empleado_sustituto_id')) {
                $table->foreignId('empleado_sustituto_id')->nullable()->after('empleado_id')->constrained('empleados')->nullOnDelete();
            }
            if (!Schema::hasColumn('empleado_vacacions', 'fecha_devolucion')) {
                $table->date('fecha_devolucion')->nullable()->after('fecha_fin');
            }
            if (!Schema::hasColumn('empleado_vacacions', 'respuesta_vista_solicitante')) {
                $table->boolean('respuesta_vista_solicitante')->default(false)->after('estado');
            }
            if (!Schema::hasColumn('empleado_vacacions', 'turno_origen_id')) {
                $table->foreignId('turno_origen_id')->nullable()->after('respuesta_vista_solicitante')->constrained('planing_turnos')->nullOnDelete();
            }
            if (!Schema::hasColumn('empleado_vacacions', 'turno_devolucion_id')) {
                $table->foreignId('turno_devolucion_id')->nullable()->after('turno_origen_id')->constrained('planing_turnos')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_vacacions', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('empleado_vacacions', 'turno_devolucion_id')) {
                $table->dropForeign(['turno_devolucion_id']);
                $columnsToDrop[] = 'turno_devolucion_id';
            }
            if (Schema::hasColumn('empleado_vacacions', 'turno_origen_id')) {
                $table->dropForeign(['turno_origen_id']);
                $columnsToDrop[] = 'turno_origen_id';
            }
            if (Schema::hasColumn('empleado_vacacions', 'empleado_sustituto_id')) {
                $table->dropForeign(['empleado_sustituto_id']);
                $columnsToDrop[] = 'empleado_sustituto_id';
            }
            if (Schema::hasColumn('empleado_vacacions', 'fecha_devolucion')) {
                $columnsToDrop[] = 'fecha_devolucion';
            }
            if (Schema::hasColumn('empleado_vacacions', 'respuesta_vista_solicitante')) {
                $columnsToDrop[] = 'respuesta_vista_solicitante';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

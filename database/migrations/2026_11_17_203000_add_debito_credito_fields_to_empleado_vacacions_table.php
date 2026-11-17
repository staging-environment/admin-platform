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
            if (!Schema::hasColumn('empleado_vacacions', 'tipo_cambio')) {
                $table->string('tipo_cambio')->nullable()->after('tipo');
            }
            if (!Schema::hasColumn('empleado_vacacions', 'empleado_deudor_id')) {
                $table->foreignId('empleado_deudor_id')->nullable()->after('empleado_sustituto_id')->constrained('empleados')->nullOnDelete();
            }
            if (!Schema::hasColumn('empleado_vacacions', 'empleado_acreedor_id')) {
                $table->foreignId('empleado_acreedor_id')->nullable()->after('empleado_deudor_id')->constrained('empleados')->nullOnDelete();
            }
            if (!Schema::hasColumn('empleado_vacacions', 'deuda_saldada')) {
                $table->boolean('deuda_saldada')->default(false)->after('respuesta_vista_solicitante');
            }
            if (!Schema::hasColumn('empleado_vacacions', 'solicitud_relacionada_id')) {
                $table->foreignId('solicitud_relacionada_id')->nullable()->after('turno_devolucion_id')->constrained('empleado_vacacions')->nullOnDelete();
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
            if (Schema::hasColumn('empleado_vacacions', 'solicitud_relacionada_id')) {
                $table->dropForeign(['solicitud_relacionada_id']);
                $columnsToDrop[] = 'solicitud_relacionada_id';
            }
            if (Schema::hasColumn('empleado_vacacions', 'empleado_acreedor_id')) {
                $table->dropForeign(['empleado_acreedor_id']);
                $columnsToDrop[] = 'empleado_acreedor_id';
            }
            if (Schema::hasColumn('empleado_vacacions', 'empleado_deudor_id')) {
                $table->dropForeign(['empleado_deudor_id']);
                $columnsToDrop[] = 'empleado_deudor_id';
            }
            if (Schema::hasColumn('empleado_vacacions', 'deuda_saldada')) {
                $columnsToDrop[] = 'deuda_saldada';
            }
            if (Schema::hasColumn('empleado_vacacions', 'tipo_cambio')) {
                $columnsToDrop[] = 'tipo_cambio';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

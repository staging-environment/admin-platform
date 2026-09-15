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
        Schema::table('empleado_documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('empleado_documentos', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('fecha_vencimiento_contrato');
            }
            if (!Schema::hasColumn('empleado_documentos', 'fecha_fin')) {
                $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            }
            if (Schema::hasColumn('empleado_documentos', 'fecha_realizacion')) {
                $table->dropColumn('fecha_realizacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('empleado_documentos', 'fecha_realizacion')) {
                $table->date('fecha_realizacion')->nullable()->after('fecha_vencimiento_contrato');
            }
            if (Schema::hasColumn('empleado_documentos', 'fecha_fin')) {
                $table->dropColumn('fecha_fin');
            }
            if (Schema::hasColumn('empleado_documentos', 'fecha_inicio')) {
                $table->dropColumn('fecha_inicio');
            }
        });
    }
};

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
            if (!Schema::hasColumn('empleado_documentos', 'fecha_inicio_contrato')) {
                $table->date('fecha_inicio_contrato')->nullable()->after('tipo_contrato');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_documentos', function (Blueprint $table) {
            if (Schema::hasColumn('empleado_documentos', 'fecha_inicio_contrato')) {
                $table->dropColumn('fecha_inicio_contrato');
            }
        });
    }
};

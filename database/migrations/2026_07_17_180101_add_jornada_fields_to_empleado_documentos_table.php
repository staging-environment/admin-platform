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
            $table->string('tipo_jornada')->nullable()->after('fecha_vencimiento_contrato');
            $table->string('tipo_jornada_otro')->nullable()->after('tipo_jornada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_documentos', function (Blueprint $table) {
            $table->dropColumn(['tipo_jornada', 'tipo_jornada_otro']);
        });
    }
};

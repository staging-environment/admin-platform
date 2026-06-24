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
        Schema::table('empleados', function (Blueprint $table) {
            $table->boolean('tiene_discapacidad')->default(false)->after('email');
            $table->date('fecha_resolucion_discapacidad')->nullable()->after('porcentaje_discapacidad');
            $table->boolean('pertenece_andalucia')->default(false)->after('fecha_resolucion_discapacidad');
            $table->string('tipo_contrato')->nullable()->after('pertenece_andalucia');
            $table->date('fecha_vencimiento_contrato')->nullable()->after('tipo_contrato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn([
                'tiene_discapacidad',
                'fecha_resolucion_discapacidad',
                'pertenece_andalucia',
                'tipo_contrato',
                'fecha_vencimiento_contrato',
            ]);
        });
    }
};

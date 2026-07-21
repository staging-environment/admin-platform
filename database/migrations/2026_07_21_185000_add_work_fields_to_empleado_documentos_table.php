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
            if (!Schema::hasColumn('empleado_documentos', 'gasolinera_codigo')) {
                $table->unsignedInteger('gasolinera_codigo')->nullable()->after('tipo_jornada_otro');
            }
            if (!Schema::hasColumn('empleado_documentos', 'puesto')) {
                $table->string('puesto')->nullable()->after('gasolinera_codigo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_documentos', function (Blueprint $table) {
            if (Schema::hasColumn('empleado_documentos', 'puesto')) {
                $table->dropColumn('puesto');
            }
            if (Schema::hasColumn('empleado_documentos', 'gasolinera_codigo')) {
                $table->dropColumn('gasolinera_codigo');
            }
        });
    }
};

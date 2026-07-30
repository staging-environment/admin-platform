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
        if (Schema::hasTable('empleado_vacacions') && !Schema::hasColumn('empleado_vacacions', 'justificante_path')) {
            Schema::table('empleado_vacacions', function (Blueprint $table) {
                $table->string('justificante_path')->nullable()->after('comentario_aprobador');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('empleado_vacacions') && Schema::hasColumn('empleado_vacacions', 'justificante_path')) {
            Schema::table('empleado_vacacions', function (Blueprint $table) {
                $table->dropColumn('justificante_path');
            });
        }
    }
};

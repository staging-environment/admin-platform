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
        if (Schema::hasTable('empleado_vacacions') && !Schema::hasColumn('empleado_vacacions', 'comentario_empleado')) {
            Schema::table('empleado_vacacions', function (Blueprint $table) {
                $table->text('comentario_empleado')->nullable()->after('estado');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('empleado_vacacions') && Schema::hasColumn('empleado_vacacions', 'comentario_empleado')) {
            Schema::table('empleado_vacacions', function (Blueprint $table) {
                $table->dropColumn('comentario_empleado');
            });
        }
    }
};

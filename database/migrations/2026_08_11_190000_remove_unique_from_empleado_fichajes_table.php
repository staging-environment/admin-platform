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
        Schema::table('empleado_fichajes', function (Blueprint $table) {
            $table->index('empleado_id');
        });

        Schema::table('empleado_fichajes', function (Blueprint $table) {
            $table->dropUnique('empleado_fichajes_empleado_id_fecha_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_fichajes', function (Blueprint $table) {
            $table->unique(['empleado_id', 'fecha']);
        });
    }
};

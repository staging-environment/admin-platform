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
        Schema::table('empleado_notificacions', function (Blueprint $table) {
            if (!Schema::hasColumn('empleado_notificacions', 'fecha_cierre')) {
                $table->date('fecha_cierre')->nullable()->after('dias_suspension');
            }
            if (!Schema::hasColumn('empleado_notificacions', 'cierre_file_path')) {
                $table->string('cierre_file_path')->nullable()->after('file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_notificacions', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_cierre',
                'cierre_file_path',
            ]);
        });
    }
};

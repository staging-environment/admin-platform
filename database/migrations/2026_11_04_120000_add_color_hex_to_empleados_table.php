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
        if (Schema::hasTable('empleados') && !Schema::hasColumn('empleados', 'color_hex')) {
            Schema::table('empleados', function (Blueprint $table) {
                $table->string('color_hex', 20)->nullable()->after('puesto');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('empleados') && Schema::hasColumn('empleados', 'color_hex')) {
            Schema::table('empleados', function (Blueprint $table) {
                $table->dropColumn('color_hex');
            });
        }
    }
};

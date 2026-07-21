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
            if (!Schema::hasColumn('empleados', 'telefono_secundario')) {
                $table->string('telefono_secundario')->nullable()->after('telefono_principal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (Schema::hasColumn('empleados', 'telefono_secundario')) {
                $table->dropColumn('telefono_secundario');
            }
        });
    }
};

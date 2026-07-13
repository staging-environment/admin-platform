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
            $table->dropColumn('incapacidad');
            $table->date('fecha_reconocimiento')->nullable()->after('porcentaje_discapacidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->string('incapacidad')->nullable()->after('porcentaje_discapacidad');
            $table->dropColumn('fecha_reconocimiento');
        });
    }
};

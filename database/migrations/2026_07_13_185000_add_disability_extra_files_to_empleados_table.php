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
            $table->string('dictamen_tecnico')->nullable()->after('resolucion_discapacidad');
            $table->string('certificado_discapacidad')->nullable()->after('dictamen_tecnico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn(['dictamen_tecnico', 'certificado_discapacidad']);
        });
    }
};

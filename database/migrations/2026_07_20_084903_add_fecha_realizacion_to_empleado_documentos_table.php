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
            $table->date('fecha_realizacion')->nullable()->after('fecha_vencimiento_contrato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_documentos', function (Blueprint $table) {
            $table->dropColumn('fecha_realizacion');
        });
    }
};

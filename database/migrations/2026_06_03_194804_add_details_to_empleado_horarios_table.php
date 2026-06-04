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
        Schema::table('empleado_horarios', function (Blueprint $table) {
            $table->json('dias_laborales')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_horarios', function (Blueprint $table) {
            $table->dropColumn(['dias_laborales', 'hora_inicio', 'hora_fin']);
        });
    }
};

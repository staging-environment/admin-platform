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
        Schema::connection('mariadb')->table('home_configs', function (Blueprint $table) {
            $table->longText('quienes_somos')->nullable()->after('subtitulo');
            $table->string('contacto_email')->nullable()->after('quienes_somos');
            $table->string('contacto_telefono')->nullable()->after('contacto_email');
            $table->string('contacto_direccion')->nullable()->after('contacto_telefono');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->table('home_configs', function (Blueprint $table) {
            $table->dropColumn(['quienes_somos', 'contacto_email', 'contacto_telefono', 'contacto_direccion']);
        });
    }
};

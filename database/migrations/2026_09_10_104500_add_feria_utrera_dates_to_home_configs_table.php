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
            $table->dateTime('feria_utrera_inicio')->nullable()->after('politica_privacidad');
            $table->dateTime('feria_utrera_fin')->nullable()->after('feria_utrera_inicio');
            $table->dateTime('feria_utrera_synced_at')->nullable()->after('feria_utrera_fin');
            $table->string('feria_utrera_source')->nullable()->after('feria_utrera_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->table('home_configs', function (Blueprint $table) {
            $table->dropColumn([
                'feria_utrera_inicio',
                'feria_utrera_fin',
                'feria_utrera_synced_at',
                'feria_utrera_source',
            ]);
        });
    }
};

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
            $table->decimal('latitud', 10, 8)->nullable()->after('contacto_direccion');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->table('home_configs', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};

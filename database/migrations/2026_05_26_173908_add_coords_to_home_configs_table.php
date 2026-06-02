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
            if (!Schema::connection('mariadb')->hasColumn('home_configs', 'latitud')) {
                $table->decimal('latitud', 10, 8)->nullable()->after('contacto_direccion');
            }
            if (!Schema::connection('mariadb')->hasColumn('home_configs', 'longitud')) {
                $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->table('home_configs', function (Blueprint $table) {
            $cols = [];
            if (Schema::connection('mariadb')->hasColumn('home_configs', 'latitud')) {
                $cols[] = 'latitud';
            }
            if (Schema::connection('mariadb')->hasColumn('home_configs', 'longitud')) {
                $cols[] = 'longitud';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

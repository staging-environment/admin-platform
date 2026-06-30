<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->boolean('tiene_incapacidad')->default(false)->after('tiene_discapacidad');
            $table->string('tipo_incapacidad')->nullable()->after('tiene_incapacidad');
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn(['tiene_incapacidad', 'tipo_incapacidad']);
        });
    }
};

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
        Schema::table('job_offers', function (Blueprint $table) {
            // Añadimos el campo active (booleano) y que por defecto sea true (1)
            $table->boolean('active')->default(true)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            // Si hacemos un rollback, eliminamos la columna para dejarlo como estaba
            $table->dropColumn('active');
        });
    }
};
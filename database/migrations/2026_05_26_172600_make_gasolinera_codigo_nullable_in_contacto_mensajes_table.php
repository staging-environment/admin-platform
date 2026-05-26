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
        Schema::connection('mariadb')->table('contacto_mensajes', function (Blueprint $table) {
            $table->unsignedInteger('gasolinera_codigo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->table('contacto_mensajes', function (Blueprint $table) {
            $table->unsignedInteger('gasolinera_codigo')->nullable(false)->change();
        });
    }
};

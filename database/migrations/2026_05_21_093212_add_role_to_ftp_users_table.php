<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ftp_users', function (Blueprint $table) {
            // Añadimos la columna 'role' después de 'user'.
            // Ponemos default('editor') para que los usuarios existentes sigan funcionando.
            $table->string('role')->default('editor')->after('user');
        });
    }

    public function down(): void
    {
        Schema::table('ftp_users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

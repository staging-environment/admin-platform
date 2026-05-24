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
        Schema::connection('mariadb_ftp')->table('ftp_users', function (Blueprint $table) {
            $table->boolean('can_upload')->default(true)->after('password');
            $table->boolean('can_download')->default(true)->after('can_upload');
            $table->boolean('can_delete')->default(true)->after('can_download');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb_ftp')->table('ftp_users', function (Blueprint $table) {
            $table->dropColumn(['can_upload', 'can_download', 'can_delete']);
        });
    }
};

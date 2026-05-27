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
        // Drop on default connection
        Schema::dropIfExists('ftp_users');

        // Drop on mariadb_ftp connection
        try {
            Schema::connection('mariadb_ftp')->dropIfExists('ftp_users');
        } catch (\Exception $e) {
            // Ignore connection errors if database connection is already removed
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback possible as the feature is completely removed
    }
};

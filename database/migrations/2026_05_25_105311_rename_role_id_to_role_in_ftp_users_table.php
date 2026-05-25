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
            // Drop the existing role_id column if it exists
            if (Schema::connection('mariadb_ftp')->hasColumn('ftp_users', 'role_id')) {
                $table->dropColumn('role_id');
            }

            // Add the new 'role' column as string with a default value
            // Place it after 'password' for consistency with the form/model
            if (!Schema::connection('mariadb_ftp')->hasColumn('ftp_users', 'role')) {
                $table->string('role')->default('editor')->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb_ftp')->table('ftp_users', function (Blueprint $table) {
            // Drop the 'role' column if it exists
            if (Schema::connection('mariadb_ftp')->hasColumn('ftp_users', 'role')) {
                $table->dropColumn('role');
            }

            // Re-add the original 'role_id' column as integer, nullable
            // Place it after 'password' to revert its original position
            if (!Schema::connection('mariadb_ftp')->hasColumn('ftp_users', 'role_id')) {
                $table->integer('role_id')->nullable()->after('password');
            }
        });
    }
};

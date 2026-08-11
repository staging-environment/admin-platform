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
        Schema::table('empleado_fichajes', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('deleted_by_email')->nullable()->after('deleted_at');
            $table->unsignedBigInteger('deleted_by_id')->nullable()->after('deleted_by_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_fichajes', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['deleted_by_email', 'deleted_by_id']);
        });
    }
};

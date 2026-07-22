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
            $table->decimal('checkin_latitude', 10, 8)->nullable()->after('server_checkin_at');
            $table->decimal('checkin_longitude', 11, 8)->nullable()->after('checkin_latitude');
            $table->decimal('checkout_latitude', 10, 8)->nullable()->after('server_checkout_at');
            $table->decimal('checkout_longitude', 11, 8)->nullable()->after('checkout_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado_fichajes', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_latitude',
                'checkin_longitude',
                'checkout_latitude',
                'checkout_longitude',
            ]);
        });
    }
};

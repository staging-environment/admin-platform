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
            $table->dropColumn('travel_possibility');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->boolean('travel_possibility')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('travel_possibility');
        });

        Schema::table('job_offers', function (Blueprint $table) {
            $table->boolean('travel_possibility')->default(false);
        });
    }
};

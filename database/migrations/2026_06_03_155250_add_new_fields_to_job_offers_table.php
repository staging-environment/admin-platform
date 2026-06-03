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
            $table->string('min_experience')->nullable();
            $table->string('salary_range')->nullable();
            $table->string('incorporation_time')->nullable();
            $table->string('years_of_experience')->nullable();
            $table->boolean('travel_possibility')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn([
                'min_experience',
                'salary_range',
                'incorporation_time',
                'years_of_experience',
                'travel_possibility',
            ]);
        });
    }
};

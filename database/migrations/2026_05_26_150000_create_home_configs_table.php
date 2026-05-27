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
        Schema::connection('mariadb')->create('home_configs', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->text('subtitulo')->nullable();
            $table->json('slider_images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->dropIfExists('home_configs');
    }
};

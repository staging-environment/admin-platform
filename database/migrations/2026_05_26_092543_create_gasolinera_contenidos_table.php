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
        Schema::create('gasolinera_contenidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gasolinera_codigo')->unique();
            $table->json('slider_images')->nullable();
            $table->text('quienes_somos')->nullable();
            $table->text('donde_estamos_texto')->nullable();
            $table->string('contacto_email')->nullable();
            $table->string('contacto_telefono')->nullable();
            $table->string('horario')->nullable();
            $table->json('servicios')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasolinera_contenidos');
    }
};

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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('dni')->unique();
            $table->date('fecha_nacimiento');
            $table->string('direccion');
            $table->string('localidad');
            $table->string('codigo_postal');
            $table->string('provincia');
            $table->string('telefono_principal');
            $table->string('telefono_secundario')->nullable();
            $table->string('email')->unique();
            $table->string('tipo_discapacidad')->nullable();
            $table->integer('porcentaje_discapacidad')->nullable();
            $table->string('incapacidad')->nullable();
            $table->string('resolucion_discapacidad')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};

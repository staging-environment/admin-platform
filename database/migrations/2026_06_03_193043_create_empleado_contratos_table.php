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
        Schema::create('empleado_contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->string('tipo_contrato'); // Indefinido, Temporal, Prácticas, etc.
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('jornada'); // Completa, Parcial
            $table->decimal('salario', 10, 2);
            $table->string('centro_trabajo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado_contratos');
    }
};

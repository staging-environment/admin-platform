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
        if (!Schema::hasTable('planing_cuadrantes')) {
            Schema::create('planing_cuadrantes', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('gasolinera_codigo'); // 1: Vistalegre, 2: Ronda Norte, 3: Rodalabota, 4: Atenas
                $table->unsignedSmallInteger('anio');
                $table->unsignedTinyInteger('mes');
                $table->string('estado', 20)->default('publicado'); // borrador, publicado
                $table->text('observaciones')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->unique(['gasolinera_codigo', 'anio', 'mes'], 'cuadrante_estacion_mes_unique');
            });
        }

        if (!Schema::hasTable('planing_turnos')) {
            Schema::create('planing_turnos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('planing_cuadrante_id')->constrained('planing_cuadrantes')->cascadeOnDelete();
                $table->date('fecha');
                $table->string('puesto', 50)->default('Puesto Único'); // Puesto Único, Puesto 1, Puesto 2, Noche
                $table->string('turno', 10); // M (Mañana), T (Tarde), N (Noche)
                $table->unsignedBigInteger('empleado_id')->nullable();
                $table->unsignedBigInteger('empleado_titular_id')->nullable();
                $table->boolean('es_sustitucion')->default(false);
                $table->string('motivo_sustitucion', 50)->nullable(); // vacaciones, baja, permiso, cambio, refuerzo, otro
                $table->text('notas')->nullable();
                $table->timestamps();

                $table->index(['planing_cuadrante_id', 'fecha']);
                $table->unique(['planing_cuadrante_id', 'fecha', 'puesto', 'turno'], 'turno_puesto_fecha_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planing_turnos');
        Schema::dropIfExists('planing_cuadrantes');
    }
};

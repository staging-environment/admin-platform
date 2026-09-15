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
        Schema::table('empleados', function (Blueprint $table) {
            if (!Schema::hasColumn('empleados', 'onboarding_completado')) {
                $table->boolean('onboarding_completado')->default(false);
            }
            if (!Schema::hasColumn('empleados', 'onboarding_paso_actual')) {
                $table->unsignedTinyInteger('onboarding_paso_actual')->default(1);
            }
            if (!Schema::hasColumn('empleados', 'onboarding_fecha_completado')) {
                $table->timestamp('onboarding_fecha_completado')->nullable();
            }
            if (!Schema::hasColumn('empleados', 'nuss')) {
                $table->string('nuss')->nullable();
            }
            if (!Schema::hasColumn('empleados', 'iban')) {
                $table->string('iban')->nullable();
            }
            if (!Schema::hasColumn('empleados', 'contacto_emergencia_nombre')) {
                $table->string('contacto_emergencia_nombre')->nullable();
            }
            if (!Schema::hasColumn('empleados', 'contacto_emergencia_telefono')) {
                $table->string('contacto_emergencia_telefono')->nullable();
            }
            if (!Schema::hasColumn('empleados', 'politicas_aceptadas_at')) {
                $table->timestamp('politicas_aceptadas_at')->nullable();
            }
            if (!Schema::hasColumn('empleados', 'onboarding_verificado_por_admin')) {
                $table->boolean('onboarding_verificado_por_admin')->default(false);
            }
            if (!Schema::hasColumn('empleados', 'onboarding_verificado_at')) {
                $table->timestamp('onboarding_verificado_at')->nullable();
            }
            if (!Schema::hasColumn('empleados', 'onboarding_verificado_user_id')) {
                $table->foreignId('onboarding_verificado_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('empleados', 'onboarding_checklist')) {
                $table->json('onboarding_checklist')->nullable();
            }
        });

        // Para los empleados existentes que ya están en alta, marcamos onboarding_completado como true para no bloquearles
        \DB::table('empleados')->update([
            'onboarding_completado' => true,
            'onboarding_verificado_por_admin' => true,
            'onboarding_fecha_completado' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (Schema::hasColumn('empleados', 'onboarding_verificado_user_id')) {
                $table->dropForeign(['onboarding_verificado_user_id']);
            }
            $cols = [
                'onboarding_completado',
                'onboarding_paso_actual',
                'onboarding_fecha_completado',
                'nuss',
                'iban',
                'contacto_emergencia_nombre',
                'contacto_emergencia_telefono',
                'politicas_aceptadas_at',
                'onboarding_verificado_por_admin',
                'onboarding_verificado_at',
                'onboarding_verificado_user_id',
                'onboarding_checklist',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('empleados', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

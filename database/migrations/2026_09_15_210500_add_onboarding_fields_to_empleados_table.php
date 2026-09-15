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
            $table->boolean('onboarding_completado')->default(false)->after('documento_baja_path');
            $table->unsignedTinyInteger('onboarding_paso_actual')->default(1)->after('onboarding_completado');
            $table->timestamp('onboarding_fecha_completado')->nullable()->after('onboarding_paso_actual');
            $table->string('nuss')->nullable()->after('dni');
            $table->string('iban')->nullable()->after('nuss');
            $table->string('contacto_emergencia_nombre')->nullable()->after('telefono_secundario');
            $table->string('contacto_emergencia_telefono')->nullable()->after('contacto_emergencia_nombre');
            $table->timestamp('politicas_aceptadas_at')->nullable()->after('onboarding_fecha_completado');
            $table->boolean('onboarding_verificado_por_admin')->default(false)->after('politicas_aceptadas_at');
            $table->timestamp('onboarding_verificado_at')->nullable()->after('onboarding_verificado_por_admin');
            $table->foreignId('onboarding_verificado_user_id')->nullable()->constrained('users')->nullOnDelete()->after('onboarding_verificado_at');
            $table->json('onboarding_checklist')->nullable()->after('onboarding_verificado_user_id');
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
            $table->dropForeign(['onboarding_verificado_user_id']);
            $table->dropColumn([
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
            ]);
        });
    }
};

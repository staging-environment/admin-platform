<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Añadir columna gasolineras_codigos a empleados
        Schema::table('empleados', function (Blueprint $table) {
            if (!Schema::hasColumn('empleados', 'gasolineras_codigos')) {
                $table->json('gasolineras_codigos')->nullable()->after('gasolinera_codigo');
            }
        });

        // 2. Añadir columna gasolineras_codigos a empleado_documentos
        Schema::table('empleado_documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('empleado_documentos', 'gasolineras_codigos')) {
                $table->json('gasolineras_codigos')->nullable()->after('gasolinera_codigo');
            }
        });

        // 3. Migrar datos existentes en empleados sin perder ningún dato
        $empleados = DB::table('empleados')->whereNotNull('gasolinera_codigo')->get();
        foreach ($empleados as $emp) {
            if (empty($emp->gasolineras_codigos) && $emp->gasolinera_codigo) {
                DB::table('empleados')->where('id', $emp->id)->update([
                    'gasolineras_codigos' => json_encode([(int) $emp->gasolinera_codigo])
                ]);
            }
        }

        // 4. Migrar datos existentes en empleado_documentos sin perder ningún dato
        $documentos = DB::table('empleado_documentos')->whereNotNull('gasolinera_codigo')->get();
        foreach ($documentos as $doc) {
            if (empty($doc->gasolineras_codigos) && $doc->gasolinera_codigo) {
                DB::table('empleado_documentos')->where('id', $doc->id)->update([
                    'gasolineras_codigos' => json_encode([(int) $doc->gasolinera_codigo])
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (Schema::hasColumn('empleados', 'gasolineras_codigos')) {
                $table->dropColumn('gasolineras_codigos');
            }
        });

        Schema::table('empleado_documentos', function (Blueprint $table) {
            if (Schema::hasColumn('empleado_documentos', 'gasolineras_codigos')) {
                $table->dropColumn('gasolineras_codigos');
            }
        });
    }
};

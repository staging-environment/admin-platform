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
            $table->unsignedInteger('virtus_codigo')->nullable()->unique()->after('id');
            
            $table->string('apellidos')->nullable()->change();
            $table->string('dni')->nullable()->change();
            $table->date('fecha_nacimiento')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('localidad')->nullable()->change();
            $table->string('codigo_postal')->nullable()->change();
            $table->string('provincia')->nullable()->change();
            $table->string('telefono_principal')->nullable()->change();
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn('virtus_codigo');
            
            // Reverting them to NOT NULL might fail if there are nulls, 
            // but this is standard down behavior
            $table->string('apellidos')->nullable(false)->change();
            $table->string('dni')->nullable(false)->change();
            $table->date('fecha_nacimiento')->nullable(false)->change();
            $table->string('direccion')->nullable(false)->change();
            $table->string('localidad')->nullable(false)->change();
            $table->string('codigo_postal')->nullable(false)->change();
            $table->string('provincia')->nullable(false)->change();
            $table->string('telefono_principal')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
        });
    }
};

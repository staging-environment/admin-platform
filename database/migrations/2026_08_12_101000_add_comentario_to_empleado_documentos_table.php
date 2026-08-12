<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('empleado_documentos') && !Schema::hasColumn('empleado_documentos', 'comentario')) {
            Schema::table('empleado_documentos', function (Blueprint $table) {
                $table->text('comentario')->nullable()->after('file_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('empleado_documentos') && Schema::hasColumn('empleado_documentos', 'comentario')) {
            Schema::table('empleado_documentos', function (Blueprint $table) {
                $table->dropColumn('comentario');
            });
        }
    }
};

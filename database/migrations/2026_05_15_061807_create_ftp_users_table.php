<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ftp_users', function (Blueprint $table) {
            $table->id();
            $table->string('user')->unique(); // El nombre de usuario
            $table->string('password');       // El hash SHA-512
            $table->string('dir');            // La ruta home
            $table->integer('uid')->default(1000);
            $table->integer('gid')->default(1000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ftp_users');
    }
};

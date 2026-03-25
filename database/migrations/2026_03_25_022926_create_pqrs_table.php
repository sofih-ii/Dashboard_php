<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pqrs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('email', 150);
            $table->string('telefono', 20)->nullable();
            $table->enum('tipo', ['peticion', 'queja', 'reclamo', 'sugerencia']);
            $table->string('asunto', 200);
            $table->text('mensaje');
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrs');
    }
};
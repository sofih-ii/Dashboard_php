<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('producto');
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['completado', 'pendiente', 'en_camino', 'devuelto'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
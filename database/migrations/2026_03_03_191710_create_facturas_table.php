<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null');
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['pagada', 'pendiente', 'vencida'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
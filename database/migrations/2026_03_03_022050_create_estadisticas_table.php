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
        Schema::create('estadisticas', function (Blueprint $table) {
            $table->id();
            $table->string('producto');
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->date('fecha');
            $table->decimal('total_ventas', 12, 2);
            $table->decimal('margen', 8, 2);
            $table->string('categoria')->default('general');
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estadisticas');
    }
};

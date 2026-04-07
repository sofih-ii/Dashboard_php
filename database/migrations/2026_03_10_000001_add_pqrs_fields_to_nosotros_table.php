<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nosotros', function (Blueprint $table) {
            $table->string('nombre')->after('id');
            $table->string('email')->after('nombre');
            $table->enum('tipo', ['peticion', 'queja', 'reclamo', 'sugerencia'])->after('email');
            $table->text('mensaje')->after('tipo');
        });
    }
    public function down(): void
    {
        Schema::table('nosotros', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'email', 'tipo', 'mensaje']);
        });
    }
};

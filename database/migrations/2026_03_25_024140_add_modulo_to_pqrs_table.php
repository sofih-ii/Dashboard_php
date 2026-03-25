<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pqrs', function (Blueprint $table) {
            $table->string('modulo', 100)->nullable()->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('pqrs', function (Blueprint $table) {
            $table->dropColumn('modulo');
        });
    }
};
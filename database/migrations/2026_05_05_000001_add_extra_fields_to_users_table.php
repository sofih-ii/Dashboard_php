<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('timezone')->default('America/Bogota')->after('avatar');
            $table->string('language')->default('es')->after('timezone');
            $table->json('notification_settings')->nullable()->after('language');
            $table->string('theme')->default('light')->after('notification_settings');
            $table->integer('per_page')->default(25)->after('theme');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar', 'timezone', 'language', 'notification_settings', 'theme', 'per_page']);
        });
    }
};

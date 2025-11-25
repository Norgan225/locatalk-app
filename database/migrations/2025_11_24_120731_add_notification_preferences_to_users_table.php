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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notifications_enabled')->default(true)->after('avatar');
            $table->string('notification_sound')->default('gentle')->after('notifications_enabled');
            $table->boolean('notification_sound_enabled')->default(true)->after('notification_sound');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notifications_enabled', 'notification_sound', 'notification_sound_enabled']);
        });
    }
};

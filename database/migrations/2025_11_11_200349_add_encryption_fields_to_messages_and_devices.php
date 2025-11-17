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
        // Add encryption fields to messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->text('encrypted_content')->nullable()->after('content');
            $table->string('encryption_key_id')->nullable()->after('encrypted_content');
            $table->boolean('is_encrypted')->default(true)->after('encryption_key_id');
        });

        // Add created_at to user_devices table for tracking
        Schema::table('user_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('user_devices', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['encrypted_content', 'encryption_key_id', 'is_encrypted']);
        });

        Schema::table('user_devices', function (Blueprint $table) {
            if (Schema::hasColumn('user_devices', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};

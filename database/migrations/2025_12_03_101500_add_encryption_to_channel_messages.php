<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('channel_messages', function (Blueprint $table) {
            $table->boolean('encrypted')->default(false)->after('attachments');
            $table->string('iv', 64)->nullable()->after('encrypted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channel_messages', function (Blueprint $table) {
            $table->dropColumn(['encrypted', 'iv']);
        });
    }
};

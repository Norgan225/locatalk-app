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
        Schema::table('messages', function (Blueprint $table) {
            // is_pinned, pinned_at, pinned_by, is_read, read_at existent déjà
            // Ajouter seulement les nouvelles colonnes

            $table->boolean('is_delivered')->default(false)->after('read_at');
            $table->timestamp('delivered_at')->nullable()->after('is_delivered');

            $table->string('message_type')->default('text')->after('type'); // text, voice, image, video, file
            $table->foreignId('reply_to')->nullable()->constrained('messages')->onDelete('set null')->after('message_type'); // Répondre à un message

            $table->index('is_delivered');
        });
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'is_delivered', 'delivered_at',
                'message_type', 'reply_to'
            ]);
        });
    }
};

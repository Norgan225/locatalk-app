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
        Schema::create('channel_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'utilisateur qui envoie
            $table->text('content');
            $table->enum('type', ['text', 'file', 'voice', 'image'])->default('text');
            $table->json('attachments')->nullable(); // Pièces jointes
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('pinned_at')->nullable();
            $table->foreignId('pinned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reply_to')->nullable()->constrained('channel_messages')->onDelete('set null'); // Réponse à un message
            $table->timestamps();
            $table->softDeletes();

            // Index pour les performances
            $table->index(['channel_id', 'created_at']);
            $table->index('user_id');
            $table->index('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_messages');
    }
};

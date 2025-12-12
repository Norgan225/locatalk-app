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
        Schema::create('channel_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('emoji', 10);
            $table->timestamps();

            // Un utilisateur ne peut réagir qu'une fois avec le même emoji à un message
            $table->unique(['channel_message_id', 'user_id', 'emoji'], 'cm_reactions_unique');
            $table->index(['channel_message_id', 'emoji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_message_reactions');
    }
};

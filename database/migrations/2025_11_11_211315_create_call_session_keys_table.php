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
        Schema::create('call_session_keys', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // ID unique de la session d'appel
            $table->foreignId('call_id')->constrained('calls')->onDelete('cascade');
            $table->text('master_key'); // Clé maître de la session (cryptée)
            $table->string('algorithm')->default('AES-256-GCM'); // Algorithme pour streaming
            $table->text('salt'); // Salt pour dérivation de clés
            $table->timestamp('created_at');
            $table->timestamp('expires_at')->nullable(); // Expiration après la fin de l'appel
            $table->boolean('is_active')->default(true);

            // Index pour recherche rapide
            $table->index('call_id');
            $table->index(['session_id', 'is_active']);
        });

        // Table pour les clés individuelles des participants
        Schema::create('call_participant_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_session_key_id')->constrained('call_session_keys')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('participant_key'); // Clé dérivée pour ce participant (cryptée)
            $table->string('key_version')->default('1'); // Version de la clé pour rotation
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            // Index
            $table->index(['call_session_key_id', 'user_id']);
            $table->unique(['call_session_key_id', 'user_id', 'key_version'], 'call_participant_keys_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_participant_keys');
        Schema::dropIfExists('call_session_keys');
    }
};

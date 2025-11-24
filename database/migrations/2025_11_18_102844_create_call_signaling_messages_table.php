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
        Schema::create('call_signaling_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // ice-candidate, offer, answer, call-ended, etc.
            $table->text('payload'); // JSON du message complet
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['call_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_signaling_messages');
    }
};

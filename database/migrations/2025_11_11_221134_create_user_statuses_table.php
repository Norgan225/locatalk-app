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
        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->enum('status', ['online', 'offline', 'away', 'busy', 'do_not_disturb'])->default('offline');
            $table->timestamp('last_seen')->nullable();
            $table->timestamp('last_activity')->nullable(); // Pour détecter inactivité
            $table->string('custom_message')->nullable(); // Message de statut personnalisé
            $table->boolean('is_invisible')->default(false); // Mode invisible
            $table->string('device_type')->nullable(); // desktop, mobile, web
            $table->timestamps();

            // Index pour recherche rapide
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_statuses');
    }
};

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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "APEC LTD"
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->enum('plan', ['free', 'pro', 'business', 'enterprise'])->default('free');
            $table->enum('subscription_status', ['active', 'trial', 'cancelled', 'expired'])->default('trial');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->integer('max_users')->default(3); // Limite selon le plan
            $table->boolean('allow_remote_access')->default(false); // Connexion à distance
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

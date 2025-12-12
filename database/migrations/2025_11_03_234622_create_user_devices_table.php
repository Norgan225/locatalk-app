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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_fingerprint', 64); // Hash unique de l'appareil
            $table->string('device_name')->nullable(); // Ex: "PC-RH-001"
            $table->string('ip_address', 45)->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_authorized')->default(true);
            $table->timestamp('first_login_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};

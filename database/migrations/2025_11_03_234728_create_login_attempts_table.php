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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('email'); // Pour tracer même si user non trouvé
            $table->string('device_fingerprint', 64)->nullable();
            $table->string('ip_address', 45);
            $table->enum('status', ['success', 'failed', 'blocked'])->default('failed');
            $table->string('reason')->nullable(); // Ex: "MAC non autorisée"
            $table->text('device_info')->nullable(); // JSON
            $table->timestamp('attempted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};

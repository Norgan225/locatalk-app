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
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->nullable()->constrained('messages')->onDelete('cascade');
            $table->string('file_name'); // Nom original du fichier
            $table->string('file_path'); // Chemin de stockage
            $table->string('file_type'); // image, video, audio, document
            $table->string('mime_type'); // image/jpeg, application/pdf, etc.
            $table->bigInteger('file_size'); // Taille en bytes
            $table->text('thumbnail_path')->nullable(); // Preview pour images/videos
            $table->integer('duration')->nullable(); // Durée pour audio/video (en secondes)
            $table->json('metadata')->nullable(); // Dimensions, codec, etc.
            $table->timestamps();

            $table->index('message_id');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'thumbnail_path',
        'duration',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Relations
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Obtenir l'URL complète du fichier
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Obtenir l'URL du thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    /**
     * Formatter la taille du fichier
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Formatter la durée (pour audio/video)
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->duration) {
            return null;
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Vérifier si c'est une image
     */
    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    /**
     * Vérifier si c'est une vidéo
     */
    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }

    /**
     * Vérifier si c'est un audio
     */
    public function isAudio(): bool
    {
        return $this->file_type === 'audio';
    }

    /**
     * Obtenir l'icône appropriée selon le type de fichier
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'image' => '🖼️',
            'video' => '🎥',
            'audio' => '🎵',
            'document' => '📄',
            'pdf' => '📕',
            'zip' => '📦',
            'code' => '💻',
        ];

        // Détection plus fine selon le MIME type
        if (str_contains($this->mime_type, 'pdf')) {
            return $icons['pdf'];
        } elseif (str_contains($this->mime_type, 'zip') || str_contains($this->mime_type, 'compressed')) {
            return $icons['zip'];
        } elseif (str_contains($this->mime_type, 'code') || in_array($this->mime_type, ['text/javascript', 'text/html', 'text/css'])) {
            return $icons['code'];
        }

        return $icons[$this->file_type] ?? $icons['document'];
    }
}

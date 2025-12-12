<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'emoji',
    ];

    /**
     * Relations
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir le nombre de réactions par emoji pour un message
     */
    public static function getReactionCounts(int $messageId): array
    {
        return static::where('message_id', $messageId)
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->get()
            ->mapWithKeys(function ($reaction) {
                return [$reaction->emoji => $reaction->count];
            })
            ->toArray();
    }

    /**
     * Vérifier si un utilisateur a déjà réagi avec un emoji spécifique
     */
    public static function hasUserReacted(int $messageId, int $userId, string $emoji): bool
    {
        return static::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->exists();
    }

    /**
     * Basculer une réaction (ajouter ou retirer)
     */
    public static function toggle(int $messageId, int $userId, string $emoji): bool
    {
        $reaction = static::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->first();

        if ($reaction) {
            $reaction->delete();
            \Log::info('MessageReaction toggled - removed', ['message_id' => $messageId, 'user_id' => $userId, 'emoji' => $emoji]);
            return false; // Réaction retirée
        } else {
            static::create([
                'message_id' => $messageId,
                'user_id' => $userId,
                'emoji' => $emoji,
            ]);
            \Log::info('MessageReaction toggled - added', ['message_id' => $messageId, 'user_id' => $userId, 'emoji' => $emoji]);
            return true; // Réaction ajoutée
        }
    }
}

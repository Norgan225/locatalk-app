<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateMessageNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->message;

        // Ne pas créer de notification si l'utilisateur s'envoie un message à lui-même
        if ($message->sender_id === $message->receiver_id) {
            return;
        }

        // Créer une notification pour le destinataire
        $notification = Notification::create([
            'user_id' => $message->receiver_id,
            'type' => 'message',
            'title' => 'Nouveau message de ' . $message->sender->name,
            'message' => $this->getNotificationMessage($message),
            'data' => [
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'sender_avatar' => $message->sender->avatar,
                'message_id' => $message->id,
                'message_type' => $message->type,
                'channel_id' => $message->receiver_id, // Pour les messages directs, on utilise receiver_id comme channel
                'conversation_type' => 'direct'
            ],
            'is_read' => false,
        ]);

        // Ici, on pourrait ajouter une logique pour envoyer des notifications push
        // via Firebase, OneSignal, ou un service similaire si nécessaire
    }

    /**
     * Générer le message de notification basé sur le type de message
     */
    private function getNotificationMessage($message): string
    {
        switch ($message->type) {
            case 'voice':
                return '📵 Message vocal';
            case 'image':
                return '🖼️ Image';
            case 'video':
                return '🎥 Vidéo';
            case 'file':
                return '📎 Fichier';
            default:
                // Pour les messages texte, afficher un aperçu
                $content = $message->is_encrypted
                    ? $message->decrypted_content
                    : $message->content;

                // Limiter la longueur du message dans la notification
                if (strlen($content) > 100) {
                    $content = substr($content, 0, 97) . '...';
                }

                return $content;
        }
    }
}

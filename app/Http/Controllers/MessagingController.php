<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Models\EncryptionKey;
use App\Services\EncryptionService;
use App\Services\LinkPreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MessagingController extends Controller
{
    protected $encryptionService;
    protected $linkPreviewService;

    public function __construct(EncryptionService $encryptionService, LinkPreviewService $linkPreviewService)
    {
        $this->encryptionService = $encryptionService;
        $this->linkPreviewService = $linkPreviewService;
    }

    /**
     * Obtenir toutes les conversations de l'utilisateur
     *
     * GET /api/messaging/conversations
     */
    public function getConversations(Request $request)
    {
        $user = $request->user();

        // Récupérer les IDs des utilisateurs en ligne
        $onlineUserIds = \App\Models\UserStatus::getOnlineUsers()->pluck('user_id')->toArray();

        // Récupérer toutes les conversations directes
        $conversations = Message::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->whereNull('channel_id')
            ->with(['sender:id,name,avatar', 'sender.currentStatus', 'receiver:id,name,avatar', 'receiver.currentStatus', 'reactions'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) use ($user) {
                // Grouper par conversation (l'autre utilisateur)
                return $message->sender_id === $user->id
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function($messages, $otherUserId) use ($user, $onlineUserIds) {
                $lastMessage = $messages->first();
                $otherUser = $lastMessage->sender_id === $user->id
                    ? $lastMessage->receiver
                    : $lastMessage->sender;

                $unreadCount = $messages->where('receiver_id', $user->id)
                                       ->where('is_read', false)
                                       ->count();

                // Déterminer le statut réel basé sur la liste des utilisateurs en ligne
                $userStatus = in_array($otherUser->id, $onlineUserIds) ? 'online' : 'offline';

                return [
                    'user_id' => $otherUser->id,
                    'user_name' => $otherUser->name,
                    'user_avatar' => $otherUser->avatar,
                    'user_status' => $userStatus,
                    'last_message' => [
                                'id' => $lastMessage->id,
                                'content' => $lastMessage->is_encrypted
                                    ? $lastMessage->decrypted_content
                                    : $lastMessage->content,
                                // Prioriser la colonne moderne `type` si elle existe
                                'type' => $lastMessage->type ?? $lastMessage->message_type,
                                'created_at' => $lastMessage->created_at->toIso8601String(),
                                'is_sent_by_me' => $lastMessage->sender_id === $user->id,
                                // Inclure attachments réduits pour affichage rapide (utile pour messages vocaux)
                                'attachments' => $lastMessage->attachments ? $lastMessage->attachments->map(function($attachment) {
                                    return [
                                        'id' => $attachment->id,
                                        'file_name' => $attachment->file_name,
                                        'file_type' => $attachment->file_type,
                                        'mime_type' => $attachment->mime_type,
                                        'file_url' => $attachment->file_url,
                                        'duration' => $attachment->formatted_duration,
                                        'icon' => $attachment->icon,
                                    ];
                                })->toArray() : [],
                            ],
                    'unread_count' => $unreadCount,
                    'total_messages' => $messages->count(),
                ];
            })
            ->values();

        return response()->json([
            'conversations' => $conversations,
            'total' => $conversations->count(),
        ]);
    }

    /**
     * Obtenir les messages d'une conversation
     *
     * GET /api/messaging/conversation/{userId}
     */
    public function getConversation(Request $request, int $userId)
    {
        $currentUser = $request->user();
        $perPage = $request->input('per_page', 50);

        // Récupérer les IDs des utilisateurs en ligne
        $onlineUserIds = \App\Models\UserStatus::getOnlineUsers()->pluck('user_id')->toArray();

        // Charger l'autre utilisateur
        $otherUser = User::with('currentStatus')->find($userId);

        $messages = Message::where(function($query) use ($currentUser, $userId) {
                $query->where('sender_id', $currentUser->id)
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function($query) use ($currentUser, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $currentUser->id);
            })
            ->with([
                'sender:id,name,avatar',
                'reactions.user:id,name,avatar',
                'attachments',
                'replyTo.sender:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Marquer comme lus les messages reçus
        Message::where('sender_id', $userId)
               ->where('receiver_id', $currentUser->id)
               ->where('is_read', false)
               ->update([
                   'is_read' => true,
                   'read_at' => now(),
               ]);

        return response()->json([
            'messages' => $messages->map(function($message) use ($currentUser) {
                return [
                    'id' => $message->id,
                    'content' => $message->is_encrypted
                        ? $message->decrypted_content
                        : $message->content,
                    // Prioriser la colonne moderne `type` puis fallback sur l'ancien champ `message_type`
                    'type' => $message->type ?? $message->message_type,
                    'sender' => $message->sender,
                    'is_sent_by_me' => $message->sender_id === $currentUser->id,
                    'is_read' => $message->is_read,
                    'read_at' => $message->read_at?->toIso8601String(),
                    'is_delivered' => $message->is_delivered,
                    'delivered_at' => $message->delivered_at?->toIso8601String(),
                    'is_pinned' => $message->is_pinned,
                    'reactions' => $message->reactions->groupBy('emoji')->map(function($reactions) {
                        return [
                            'count' => $reactions->count(),
                            'users' => $reactions->pluck('user')->toArray(),
                        ];
                    }),
                    'attachments' => $message->relationLoaded('attachments')
                        ? $message->getRelation('attachments')->map(function($attachment) {
                            return [
                                'id' => $attachment->id,
                                'file_name' => $attachment->file_name,
                                'file_type' => $attachment->file_type,
                                'mime_type' => $attachment->mime_type,
                                'file_size' => $attachment->formatted_size,
                                'file_url' => $attachment->file_url,
                                'thumbnail_url' => $attachment->thumbnail_url,
                                'duration' => $attachment->duration,
                                'formatted_duration' => $attachment->formatted_duration,
                                'icon' => $attachment->icon,
                            ];
                        })
                        : [],
                    'reply_to' => $message->replyTo ? [
                        'id' => $message->replyTo->id,
                        'content' => substr($message->replyTo->content, 0, 100),
                        'sender_name' => $message->replyTo->sender->name,
                    ] : null,
                    'created_at' => $message->created_at->toIso8601String(),
                ];
            }),
            'user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar,
                'status' => in_array($otherUser->id, $onlineUserIds) ? 'online' : 'offline',
            ],
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'total_pages' => $messages->lastPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Envoyer un message
     *
     * POST /api/messaging/send
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'nullable|string',
            'type' => 'nullable|in:text,voice,image,video,file',
            'reply_to' => 'nullable|integer|exists:messages,id',
            'attachment_ids' => 'nullable|array',
            'attachment_ids.*' => 'integer|exists:message_attachments,id',
        ]);

        // Validation personnalisée: content requis sauf si c'est un message vocal
        $validator->after(function ($validator) use ($request) {
            if ($request->type !== 'voice' && !$request->input('content')) {
                $validator->errors()->add('content', 'Le contenu est requis pour ce type de message');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $receiverId = $request->receiver_id;
    $content = $request->input('content');

        // Cryptage E2E pour messages directs
        $encryptionKey = EncryptionKey::getOrCreateKey($user->id, $receiverId);
        $key = $encryptionKey->getDecryptedKey();

        $encrypted = $this->encryptionService->encrypt($content, $key);

        $message = Message::create([
            'organization_id' => $user->organization_id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'content' => $content ?: 'Message vocal',
            'encrypted_content' => $content ? $encrypted['iv'] . ':' . $encrypted['encrypted'] : null,
            'encryption_key_id' => $content ? $encryptionKey->key_id : null,
            'is_encrypted' => $content ? true : false,
            'type' => $request->type ?? 'text',
            'reply_to' => $request->reply_to,
            'is_delivered' => false,
        ]);

        // Lier les attachments au message
        if ($request->attachment_ids) {
            MessageAttachment::whereIn('id', $request->attachment_ids)
                ->whereNull('message_id') // S'assurer qu'ils ne sont pas déjà liés
                ->update(['message_id' => $message->id]);
        }

        // Charger les relations
        $message->load(['sender:id,name,avatar', 'replyTo.sender:id,name', 'attachments']);

        // Broadcast en temps réel
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message envoyé',
            'data' => [
                'id' => $message->id,
                'content' => $message->decrypted_content,
                'type' => $message->type,
                'sender' => $message->sender,
                'is_encrypted' => true,
                'created_at' => $message->created_at->toIso8601String(),
                'reply_to' => $message->replyTo ? [
                    'id' => $message->replyTo->id,
                    'content' => substr($message->replyTo->content, 0, 100),
                    'sender_name' => $message->replyTo->sender->name,
                ] : null,
            ],
        ], 201);
    }

    /**
     * Ajouter une réaction à un message
     *
     * POST /api/messaging/messages/{messageId}/react
     */
    public function addReaction(Request $request, int $messageId)
    {
        $validator = Validator::make($request->all(), [
            'emoji' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = Message::findOrFail($messageId);
        $user = $request->user();

        try {

            // If client requests a replace, remove any existing reactions by this user on this message
            if ($request->boolean('replace')) {
                $deleted = MessageReaction::where('message_id', $messageId)->where('user_id', $user->id)->delete();
                \Illuminate\Support\Facades\Log::info('MessageReaction replace requested - deleted existing', ['message_id' => $messageId, 'user_id' => $user->id, 'deleted_count' => $deleted]);
                // Create the new reaction
                $reaction = MessageReaction::create([
                    'message_id' => $messageId,
                    'user_id' => $user->id,
                    'emoji' => $request->emoji,
                ]);
                \Illuminate\Support\Facades\Log::info('MessageReaction replace - created', ['message_id' => $messageId, 'user_id' => $user->id, 'emoji' => $request->emoji, 'id' => $reaction->id]);
                $added = true;
            } else {
                $added = MessageReaction::toggle($messageId, $user->id, $request->emoji);
            }

            // Recharger les réactions avec utilisateurs pour un payload plus riche
            $message->load(['reactions.user']);

            // Broadcast le changement (avec objet message enrichi)
            broadcast(new \App\Events\MessageReactionChanged($message))->toOthers();

            // Construire la même forme que getConversation (emoji => {count, users: [...]})
            $reactions = $message->reactions->groupBy('emoji')->map(function($reactionsGroup) {
                return [
                    'count' => $reactionsGroup->count(),
                    'users' => $reactionsGroup->pluck('user')->toArray(),
                ];
            })->toArray();

            return response()->json([
                'message' => $added ? 'Réaction ajoutée' : 'Réaction retirée',
                'reactions' => $reactions,
            ]);
        } catch (\Throwable $e) {
            // Log détaillé pour faciliter le debug
            \Illuminate\Support\Facades\Log::error('Failed to toggle reaction', [
                'message_id' => $messageId,
                'user_id' => $user?->id,
                'emoji' => $request->emoji,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erreur serveur lors du traitement de la réaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Épingler un message
     *
     * POST /api/messaging/messages/{messageId}/pin
     */
    public function pinMessage(Request $request, int $messageId)
    {
        \Illuminate\Support\Facades\Log::info('Pin message attempt', [
            'messageId' => $messageId,
            'user' => $request->user(),
            'headers' => $request->headers->all()
        ]);

        $message = Message::findOrFail($messageId);
        $user = $request->user();

        if (!$user) {
            \Illuminate\Support\Facades\Log::error('No authenticated user for pin message');
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Vérifier que l'utilisateur est impliqué dans la conversation
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            \Illuminate\Support\Facades\Log::warning('Unauthorized pin attempt', [
                'user_id' => $user->id,
                'message_sender' => $message->sender_id,
                'message_receiver' => $message->receiver_id,
                'channel_id' => $message->channel_id
            ]);
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        \Illuminate\Support\Facades\Log::info('Pinning message', ['message_id' => $messageId, 'user_id' => $user->id]);
        $message->pin($user->id);

        return response()->json([
            'message' => 'Message épinglé',
        ]);
    }

    /**
     * Dépingler un message
     *
     * POST /api/messaging/messages/{messageId}/unpin
     */
    public function unpinMessage(Request $request, int $messageId)
    {
        $message = Message::findOrFail($messageId);
        $user = $request->user();

        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $message->unpin();

        return response()->json([
            'message' => 'Message dépinglé',
        ]);
    }

    /**
     * Obtenir les messages épinglés d'une conversation
     *
     * GET /api/messaging/conversation/{userId}/pinned
     */
    public function getPinnedMessages(Request $request, int $userId)
    {
        $currentUser = $request->user();

        $pinnedMessages = Message::where('is_pinned', true)
            ->where(function($query) use ($currentUser, $userId) {
                $query->where(function($q) use ($currentUser, $userId) {
                    $q->where('sender_id', $currentUser->id)
                      ->where('receiver_id', $userId);
                })
                ->orWhere(function($q) use ($currentUser, $userId) {
                    $q->where('sender_id', $userId)
                      ->where('receiver_id', $currentUser->id);
                });
            })
            ->with(['sender:id,name,avatar'])
            ->orderBy('pinned_at', 'desc')
            ->get();

        return response()->json([
            'pinned_messages' => $pinnedMessages->map(function($message) use ($currentUser) {
                return [
                    'id' => $message->id,
                    'content' => $message->is_encrypted
                        ? $message->decrypted_content
                        : $message->content,
                    'sender' => $message->sender,
                    'pinned_by' => $message->pinned_by ? User::find($message->pinned_by)?->name : null,
                    'pinned_at' => $message->pinned_at ? $message->pinned_at->toIso8601String() : null,
                    'created_at' => $message->created_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Uploader un fichier attaché
     *
     * POST /api/messaging/upload
     */
    public function uploadAttachment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:51200', // 50MB max
            'message_id' => 'nullable|integer|exists:messages,id',
            'receiver_id' => 'nullable|integer|exists:users,id',
            'duration' => 'nullable|integer|min:1|max:3600', // Durée en secondes, max 1h
        ]);

        // Validation personnalisée: au moins un des deux doit être présent
        $validator->after(function ($validator) use ($request) {
            if (!$request->message_id && !$request->receiver_id) {
                $validator->errors()->add('message_id', 'Soit message_id soit receiver_id doit être fourni');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $messageId = $request->message_id;
        $receiverId = $request->receiver_id;

        // 🎯 Pour les uploads temporaires (pas de message_id), créer d'abord le message
        // SAUF pour les fichiers audio (vocaux) qui seront liés lors de l'envoi du message
        if (!$messageId && $receiverId) {
            // Déterminer le type de fichier d'abord
            $mimeType = $file->getMimeType();
            $fileType = 'document';
            if (str_starts_with($mimeType, 'image/')) {
                $fileType = 'image';
            } elseif (str_starts_with($mimeType, 'audio/') ||
                      str_contains($mimeType, 'webm') ||
                      str_contains($mimeType, 'ogg')) {
                $fileType = 'audio';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $fileType = 'video';
            }

            // Pour les fichiers audio, ne pas créer de message temporaire
            if ($fileType !== 'audio') {
                $message = Message::create([
                    'organization_id' => $request->user()->organization_id,
                    'sender_id' => $request->user()->id,
                    'receiver_id' => $receiverId,
                    'content' => '',
                    'type' => 'text', // Type temporaire
                    'is_encrypted' => false,
                ]);
                $messageId = $message->id;
                Log::info('Message temporaire créé pour upload:', ['message_id' => $messageId]);
            }
        }

        // Si message_id est fourni, vérifier qu'il appartient à l'utilisateur
        if ($messageId) {
            $message = Message::findOrFail($messageId);
            if ($message->sender_id !== $request->user()->id) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
        }

        // Déterminer le type de fichier
        $mimeType = $file->getMimeType();
        $fileType = 'document';

        // 🎯 Priorité aux fichiers audio/vidéo pour les messages vocaux
        if (str_starts_with($mimeType, 'image/')) {
            $fileType = 'image';
        } elseif (str_starts_with($mimeType, 'audio/') ||
                  str_contains($mimeType, 'webm') ||
                  str_contains($mimeType, 'ogg')) {
            $fileType = 'audio';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $fileType = 'video';
        }

        // 🎯 Pour les fichiers audio, optimiser avec FFmpeg
        if ($fileType === 'audio') {
            Log::info('🎤 Optimisation audio lancée');
            $optimizedResult = $this->optimizeAudioWithFFmpeg($file);
            if ($optimizedResult) {
                $path = $optimizedResult['path'];
                $mimeType = $optimizedResult['mime'];
                Log::info('✅ Audio optimisé:', $optimizedResult['log']);
            } else {
                $path = $file->store('messages/attachments', 'public');
                Log::warning('⚠️ Audio stocké sans optimisation (FFmpeg échoué)');
            }
        } else {
            // Stocker le fichier normalement
            $path = $file->store('messages/attachments', 'public');
        }

        // Créer le thumbnail pour les images
        $thumbnailPath = null;
        if ($fileType === 'image') {
            $thumbnailPath = $this->createThumbnail($file, $path);
        }

        // Extraire la durée pour les fichiers audio/vidéo
        $duration = $request->duration;
        if (($fileType === 'audio' || $fileType === 'video') && !$duration) {
            $duration = $this->getMediaDuration($file->getRealPath());
        }

        $attachment = MessageAttachment::create([
            'message_id' => $messageId, // Toujours défini maintenant
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'thumbnail_path' => $thumbnailPath,
            'duration' => $duration,
        ]);

        return response()->json([
            'message' => 'Fichier uploadé',
            'attachment' => [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'file_type' => $attachment->file_type,
                'file_size' => $attachment->formatted_size,
                'file_url' => $attachment->file_url,
                'thumbnail_url' => $attachment->thumbnail_url,
                'duration' => $attachment->duration,
                'formatted_duration' => $attachment->formatted_duration,
                'icon' => $attachment->icon,
            ],
        ], 201);
    }

    /**
     * Créer un thumbnail pour les images
     */
    private function createThumbnail($file, $originalPath): ?string
    {
        try {
            $img = \Intervention\Image\Facades\Image::make($file); // Vérifier que Intervention Image est bien installé
            $img->fit(300, 300);

            $thumbnailPath = str_replace('attachments/', 'attachments/thumbnails/', $originalPath);
            $fullPath = storage_path('app/public/' . $thumbnailPath);

            // Créer le dossier si nécessaire
            $dir = dirname($fullPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $img->save($fullPath);

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur création thumbnail: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🎯 Optimiser fichier audio avec FFmpeg (conversion .opus)
     * Compression intelligente pour qualité WhatsApp/Telegram
     */
private function optimizeAudioWithFFmpeg($file): ?array
{
    try {
        $ffmpegPath = exec('which ffmpeg');
        if (empty($ffmpegPath)) {
            \Illuminate\Support\Facades\Log::warning('FFmpeg non installé');
            return null;
        }

        $inputPath = $file->getRealPath();
        $mimeType = $file->getMimeType();

        // 🎯 Vérifier si c'est déjà du Opus
        $isAlreadyOpus = (
            str_contains($mimeType, 'opus') ||
            str_contains($mimeType, 'webm') ||
            str_contains($mimeType, 'ogg')
        );

        // 🎯 Si déjà Opus + qualité OK, juste copier le fichier
        if ($isAlreadyOpus && $file->getSize() < 1024 * 1024) { // < 1MB = probablement déjà optimisé
            $outputFilename = uniqid('voice_') . '.opus';
            $outputPath = 'messages/attachments/' . $outputFilename;

            // Copier directement sans re-encoder
            $file->storeAs('messages/attachments', $outputFilename, 'public');

            \Illuminate\Support\Facades\Log::info('Audio déjà optimisé, copie directe', [
                'mime' => $mimeType,
                'size' => $file->getSize()
            ]);

            return [
                'path' => $outputPath,
                'mime' => 'audio/opus',
                'log' => ['status' => 'copied', 'size' => $file->getSize()]
            ];
        }

        // 🎯 Sinon, optimiser SEULEMENT si nécessaire
        $outputFilename = uniqid('voice_') . '.opus';
        $outputPath = storage_path('app/public/messages/attachments/' . $outputFilename);

        $dir = dirname($outputPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // 🎯 Bitrate PLUS ÉLEVÉ pour éviter la perte de qualité
        // Si le fichier source est déjà Opus, on monte à 96kbps
        $bitrate = $isAlreadyOpus ? '96k' : '64k';

        $command = sprintf(
            '%s -i %s -c:a libopus -b:a %s -ac 1 -ar 48000 -application voip -vbr on -compression_level 10 %s 2>&1',
            escapeshellarg($ffmpegPath),
            escapeshellarg($inputPath),
            $bitrate,
            escapeshellarg($outputPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            \Illuminate\Support\Facades\Log::error('FFmpeg échec: ' . implode("\n", $output));
            return null;
        }

        $originalSize = filesize($inputPath);
        $optimizedSize = filesize($outputPath);

        \Illuminate\Support\Facades\Log::info('Audio optimisé', [
            'original' => $originalSize,
            'optimized' => $optimizedSize,
            'ratio' => round((1 - $optimizedSize / $originalSize) * 100, 2) . '%',
            'bitrate' => $bitrate
        ]);

        return [
            'path' => 'messages/attachments/' . $outputFilename,
            'mime' => 'audio/opus',
            'log' => [
                'original' => $originalSize,
                'optimized' => $optimizedSize,
                'ratio' => round((1 - $optimizedSize / $originalSize) * 100, 2) . '%',
                'bitrate' => $bitrate
            ]
        ];

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erreur audio: ' . $e->getMessage());
        return null;
    }
}

    /**
     * Marquer un message comme livré
     *
     * POST /api/messaging/messages/{messageId}/delivered
     */
    public function markAsDelivered(Request $request, int $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->receiver_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $message->markAsDelivered();

        // Broadcast au sender
        broadcast(new \App\Events\MessageDelivered($message))->toOthers();

        return response()->json([
            'message' => 'Marqué comme livré',
        ]);
    }

    /**
     * Rechercher dans une conversation
     *
     * GET /api/messaging/conversation/{userId}/search
     */
    public function searchInConversation(Request $request, int $userId)
    {
        $query = (string) $request->input('q');
        $currentUser = $request->user();
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        if (!$query) {
            return response()->json(['results' => [], 'pagination' => ['current_page' => 1, 'per_page' => $perPage, 'total' => 0, 'last_page' => 0]]);
        }

        $baseQuery = Message::where(function($q) use ($currentUser, $userId) {
                $q->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function($q) use ($currentUser, $userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $currentUser->id);
            })
            ->where('content', 'LIKE', "%{$query}%")
            ->with('sender:id,name,avatar')
            ->orderBy('created_at', 'desc');

    // Cloner la requête pour calculer le total d'occurrences sans pagination
    $countQuery = clone $baseQuery;

    $paginated = $baseQuery->paginate($perPage, ['*'], 'page', $page);

    $results = $paginated->getCollection()->map(function($message) use ($query) {
            // Utiliser le contenu décrypté si nécessaire
            $content = $message->is_encrypted ? $message->decrypted_content : $message->content;

            // Compter le nombre d'occurrences du terme (insensible à la casse)
            $matchCount = 0;
            if ($content) {
                $matchCount = substr_count(mb_strtolower($content), mb_strtolower($query));
            }

            return [
                'id' => $message->id,
                'content' => $content,
                'sender' => $message->sender,
                'created_at' => $message->created_at->toIso8601String(),
                'match_count' => $matchCount,
            ];
        });

        // Calculer le nombre total d'occurrences sur l'ensemble des messages correspondants
        try {
            $allMatches = $countQuery->get();
            $totalOccurrences = $allMatches->reduce(function($carry, $m) use ($query) {
                $content = $m->is_encrypted ? $m->decrypted_content : $m->content;
                if (!$content) return $carry;
                return $carry + substr_count(mb_strtolower($content), mb_strtolower($query));
            }, 0);
        } catch (\Exception $e) {
            // En cas d'erreur, ne pas bloquer la recherche
            \Illuminate\Support\Facades\Log::warning('Erreur calcul total_occurrences: ' . $e->getMessage());
            $totalOccurrences = array_sum($results->pluck('match_count')->toArray());
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'total_occurrences' => $totalOccurrences,
            ],
        ]);
    }

    /**
     * Supprimer un message
     *
     * DELETE /api/messaging/messages/{messageId}
     */
    public function deleteMessage(Request $request, int $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $message->delete();

        // Broadcast la suppression
        broadcast(new \App\Events\MessageDeleted($messageId))->toOthers();

        return response()->json([
            'message' => 'Message supprimé',
        ]);
    }

    /**
     * Indiquer qu'un utilisateur est en train de taper
     *
     * POST /api/messaging/typing
     */
    public function typing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_user_id' => 'required|integer|exists:users,id',
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        broadcast(new \App\Events\UserTyping(
            $user->id,
            $user->name,
            $request->conversation_user_id,
            $request->is_typing
        ))->toOthers();

        return response()->json([
            'message' => 'Statut de frappe envoyé',
        ]);
    }

    /**
     * Extraire les métadonnées d'une URL pour l'aperçu
     *
     * POST /api/messaging/link-preview
     */
    public function getLinkPreview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $metadata = $this->linkPreviewService->extractMetadata($request->url);

        if (!$metadata) {
            return response()->json([
                'message' => 'Impossible d\'extraire les métadonnées',
            ], 404);
        }

        return response()->json([
            'preview' => $metadata,
        ]);
    }

    /**
     * Obtenir la liste des utilisateurs disponibles pour la messagerie
     *
     * GET /api/messaging/users
     */
    public function getAvailableUsers(Request $request)
    {
        $currentUser = $request->user();

        // Récupérer les IDs des utilisateurs en ligne
        $onlineUserIds = \App\Models\UserStatus::getOnlineUsers()->pluck('user_id')->toArray();

        // Récupérer tous les utilisateurs de la même organisation
        $users = User::where('organization_id', $currentUser->organization_id)
            ->where('id', '!=', $currentUser->id)
            ->where('status', 'active')
            ->select('id', 'name', 'email', 'avatar', 'role')
            ->orderBy('name')
            ->get()
            ->map(function($user) use ($onlineUserIds) {
                // Déterminer le statut réel basé sur la liste des utilisateurs en ligne
                $status = in_array($user->id, $onlineUserIds) ? 'online' : 'offline';

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'role' => $user->role,
                    'status' => $status,
                ];
            });

        return response()->json([
            'users' => $users,
            'total' => $users->count(),
        ]);
    }

    /**
     * Obtenir la durée d'un fichier média (audio/vidéo)
     */
    private function getMediaDuration(string $filePath): ?int
    {
        try {
            // Utiliser ffprobe si disponible, sinon une approche basique
            if (function_exists('shell_exec') && shell_exec('which ffprobe 2>/dev/null')) {
                $command = "ffprobe -v quiet -print_format json -show_format -show_streams \"$filePath\"";
                $output = shell_exec($command);
                $data = json_decode($output, true);

                if ($data && isset($data['format']['duration'])) {
                    return (int) ceil($data['format']['duration']);
                }

                // Chercher dans les streams
                if (isset($data['streams'])) {
                    foreach ($data['streams'] as $stream) {
                        if (isset($stream['duration'])) {
                            return (int) ceil($stream['duration']);
                        }
                    }
                }
            }

            // Fallback: estimation basée sur la taille du fichier (très approximatif)
            // Pour les fichiers audio, ~1MB ≈ 1 minute à 128kbps
            $fileSizeMB = filesize($filePath) / (1024 * 1024);
            $estimatedMinutes = $fileSizeMB * 0.5; // Approximation
            return (int) ceil($estimatedMinutes * 60);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Erreur extraction durée média: ' . $e->getMessage());
            return null;
        }
    }
}

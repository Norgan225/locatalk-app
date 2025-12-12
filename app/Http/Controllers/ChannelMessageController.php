<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Events\ChannelUserTyping;
use App\Models\ChannelMessage;
use App\Models\ChannelMessageReaction;
use App\Models\ChannelEncryptionKey;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\EncryptionService;

class ChannelMessageController extends Controller
{
    protected EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Récupérer les messages d'un canal
     */
    public function index(Request $request, $channelId)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($channelId);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        // Récupérer les messages avec pagination
        $messages = ChannelMessage::with(['user:id,name,avatar', 'replyTo.user:id,name', 'reactions.user:id,name'])
            ->where('channel_id', $channelId)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50));

        // Formater les messages pour la réponse
        $formattedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'encrypted' => (bool) ($message->encrypted ?? false),
                'iv' => $message->iv,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'user_avatar' => $message->user->avatar,
                'created_at' => $message->created_at,
                'is_pinned' => $message->is_pinned,
                'attachments' => $message->attachments ?? [],
                'reply_to' => $message->replyTo ? [
                    'id' => $message->replyTo->id,
                    'content' => $message->replyTo->content,
                    'user_name' => $message->replyTo->user->name,
                    'encrypted' => (bool) ($message->replyTo->encrypted ?? false),
                    'iv' => $message->replyTo->iv,
                ] : null,
                'reactions' => $message->reactions->groupBy('emoji')->map(function ($reactions, $emoji) {
                    return [
                        'emoji' => $emoji,
                        'count' => $reactions->count(),
                        'users' => $reactions->pluck('user.name')->toArray(),
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'messages' => $formattedMessages,
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    public function encryptionKey(Request $request, $channelId)
    {
        $user = $request->user();
        $channel = Channel::findOrFail($channelId);

        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        $record = ChannelEncryptionKey::where('channel_id', $channel->id)->first();

        if (!$record) {
            $rawKey = $this->encryptionService->generateConversationKey();
            $record = ChannelEncryptionKey::create([
                'channel_id' => $channel->id,
                'created_by' => $user->id,
                'encrypted_key' => $this->encryptionService->encryptKey($rawKey),
                'algorithm' => 'AES-256-GCM',
            ]);
        } else {
            $rawKey = $this->encryptionService->decryptKey($record->encrypted_key);
        }

        return response()->json([
            'key' => $rawKey,
            'algorithm' => $record->algorithm,
        ]);
    }

    public function updateEncryptionKey(Request $request, $channelId)
    {
        $validator = Validator::make($request->all(), [
            'key' => ['required', 'string', 'min:40', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $channel = Channel::findOrFail($channelId);

        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        $rawKey = trim($request->input('key'));

        $record = ChannelEncryptionKey::updateOrCreate(
            ['channel_id' => $channel->id],
            [
                'created_by' => $user->id,
                'encrypted_key' => $this->encryptionService->encryptKey($rawKey),
                'algorithm' => 'AES-256-GCM',
            ]
        );

        return response()->json([
            'message' => 'Clé de canal mise à jour',
            'updated_at' => $record->updated_at,
        ]);
    }

    /**
     * Créer un nouveau message dans un canal
     */
    public function store(Request $request, $channelId)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            'type' => 'in:text,file,voice,image',
            'reply_to' => 'nullable|exists:channel_messages,id',
            'attachments' => 'nullable|array',
            'encrypted' => 'nullable|boolean',
            'iv' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($channelId);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        // Créer le message
        $message = ChannelMessage::create([
            'channel_id' => $channel->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'reply_to' => $request->reply_to,
            'attachments' => $request->attachments,
            'encrypted' => (bool) $request->encrypted,
            'iv' => $request->iv,
        ]);

        // Charger les relations pour la réponse
        $message->load(['user:id,name,avatar', 'replyTo.user:id,name']);

        // Diffuser le message via WebSocket
        broadcast(new \App\Events\ChannelMessageSent($message))->toOthers();

        ActivityLog::log('channel_message_sent', "Message envoyé dans le canal: {$channel->name}");

        return response()->json([
            'message' => 'Message envoyé avec succès.',
            'data' => [
                'id' => $message->id,
                'channel_id' => $message->channel_id,
                'content' => $message->content,
                'type' => $message->type,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'user_avatar' => $message->user->avatar,
                'created_at' => $message->created_at,
                'encrypted' => $message->encrypted,
                'iv' => $message->iv,
                'attachments' => $message->attachments ?? [],
                'reply_to' => $message->replyTo ? [
                    'id' => $message->replyTo->id,
                    'content' => $message->replyTo->content,
                    'user_name' => $message->replyTo->user->name,
                    'encrypted' => (bool) ($message->replyTo->encrypted ?? false),
                    'iv' => $message->replyTo->iv,
                ] : null,
            ]
        ], 201);
    }

    /**
     * Épingler/Désépingler un message
     */
    public function typing(Request $request, $channelId)
    {
        $request->validate([
            'is_typing' => 'sometimes|boolean',
        ]);

        $user = $request->user();
        $channel = Channel::findOrFail($channelId);

        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        $isTyping = (bool) ($request->input('is_typing', true));

        broadcast(new ChannelUserTyping($channel->id, $user, $isTyping))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Épingler/Désépingler un message
     */
    public function togglePin(Request $request, $channelId, $messageId)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($channelId);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        $message = ChannelMessage::where('channel_id', $channelId)
            ->where('id', $messageId)
            ->firstOrFail();

        if ($message->is_pinned) {
            $message->unpin();
            $action = 'désépinglé';
        } else {
            $message->pin($user);
            $action = 'épinglé';
        }

        ActivityLog::log('channel_message_pinned', "Message {$action} dans le canal: {$channel->name}");

        return response()->json([
            'message' => "Message {$action} avec succès.",
            'is_pinned' => $message->is_pinned
        ]);
    }

    /**
     * Ajouter une réaction à un message
     */
    public function addReaction(Request $request, $channelId, $messageId)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'emoji' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($channelId);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        $message = ChannelMessage::where('channel_id', $channelId)
            ->where('id', $messageId)
            ->firstOrFail();

        // Créer ou supprimer la réaction
        $existingReaction = ChannelMessageReaction::where('channel_message_id', $messageId)
            ->where('user_id', $user->id)
            ->where('emoji', $request->emoji)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();
            $action = 'removed';
        } else {
            ChannelMessageReaction::create([
                'channel_message_id' => $messageId,
                'user_id' => $user->id,
                'emoji' => $request->emoji,
            ]);
            $action = 'added';
        }

        return response()->json([
            'message' => 'Réaction ' . ($action === 'added' ? 'ajoutée' : 'supprimée') . ' avec succès.',
            'action' => $action
        ]);
    }

    /**
     * Supprimer un message (soft delete)
     */
    public function destroy(Request $request, $channelId, $messageId)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($channelId);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        $message = ChannelMessage::where('channel_id', $channelId)
            ->where('id', $messageId)
            ->firstOrFail();

        // Vérifier que l'utilisateur est l'auteur du message ou admin
        if ($message->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'Vous ne pouvez supprimer que vos propres messages.'], 403);
        }

        $message->delete();

        ActivityLog::log('channel_message_deleted', "Message supprimé dans le canal: {$channel->name}");

        return response()->json(['message' => 'Message supprimé avec succès.']);
    }

    /**
     * Récupérer les messages épinglés d'un canal
     */
    public function getPinnedMessages(Request $request, $channelId)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($channelId);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        $pinnedMessages = ChannelMessage::with(['user:id,name,avatar'])
            ->where('channel_id', $channelId)
            ->where('is_pinned', true)
            ->orderBy('pinned_at', 'desc')
            ->get();

        return response()->json(['pinned_messages' => $pinnedMessages]);
    }
}

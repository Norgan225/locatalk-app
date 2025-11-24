<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Channel;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of messages.
     * Can filter by conversation (sender/receiver) or channel.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Message::with(['sender', 'receiver', 'channel', 'organization']);

        // Filter by organization if not owner
        if ($user && !$user->isOwner()) {
            $query->where('organization_id', $user->organization_id);
        }

        // Filter by conversation (direct messages)
        if ($request->has('conversation_with')) {
            $otherUserId = $request->conversation_with;
            $query->where(function ($q) use ($user, $otherUserId) {
                $q->where(function ($subQ) use ($user, $otherUserId) {
                    $subQ->where('sender_id', $user->id)
                        ->where('receiver_id', $otherUserId);
                })->orWhere(function ($subQ) use ($user, $otherUserId) {
                    $subQ->where('sender_id', $otherUserId)
                        ->where('receiver_id', $user->id);
                });
            })->whereNull('channel_id');
        }

        // Filter by channel
        if ($request->has('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        // Filter unread messages
        if ($request->boolean('unread')) {
            $query->where('receiver_id', $user->id)
                ->where('is_read', false);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 50));

        if ($request->wantsJson()) {
            return response()->json($messages, 200);
        }

        return view('messages.index', ['messages' => $messages]);
    }

    /**
     * Get list of conversations (unique users/channels).
     */
    public function conversations(Request $request)
    {
        $user = $request->user();

        // Get direct message conversations
        $directMessages = Message::where('organization_id', $user->organization_id)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->whereNull('channel_id')
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by conversation partner
        $conversations = [];
        $seen = [];

        foreach ($directMessages as $message) {
            $partnerId = $message->sender_id === $user->id
                ? $message->receiver_id
                : $message->sender_id;

            if (!in_array($partnerId, $seen)) {
                $seen[] = $partnerId;
                $partner = User::find($partnerId);

                if ($partner) {
                    $unreadCount = Message::where('sender_id', $partnerId)
                        ->where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();

                    $conversations[] = [
                        'type' => 'direct',
                        'partner' => $partner,
                        'last_message' => $message,
                        'unread_count' => $unreadCount,
                    ];
                }
            }
        }

        // Get channel conversations
        $channels = $user->channels()
            ->with(['messages' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }])
            ->get();

        foreach ($channels as $channel) {
            $unreadCount = Message::where('channel_id', $channel->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();

            $conversations[] = [
                'type' => 'channel',
                'channel' => $channel,
                'last_message' => $channel->messages->first(),
                'unread_count' => $unreadCount,
            ];
        }

        if ($request->wantsJson()) {
            return response()->json(['data' => $conversations], 200);
        }

        return view('messages.conversations', ['conversations' => $conversations]);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Non authentifié.'], 401)
                : redirect()->route('login');
        }

        $v = Validator::make($request->all(), [
            'receiver_id' => 'required_without:channel_id|nullable|exists:users,id',
            'channel_id' => 'required_without:receiver_id|nullable|exists:channels,id',
            'content' => 'required|string',
            'type' => 'nullable|in:text,image,file,video,audio',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        // Verify channel access if sending to channel
        if ($request->channel_id) {
            $channel = Channel::findOrFail($request->channel_id);

            // Check if user is member of the channel
            if (!$channel->users()->where('users.id', $user->id)->exists()) {
                return $request->wantsJson()
                    ? response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403)
                    : redirect()->back()->with('error', 'Accès non autorisé.');
            }
        }

        // Crypter le message pour les conversations directes
        $encryptionService = app(\App\Services\EncryptionService::class);
        $encryptedContent = null;
        $encryptionKeyId = null;
        $isEncrypted = false;

        if ($request->receiver_id) {
            // Message direct - Cryptage E2E
            $encryptionKey = \App\Models\EncryptionKey::getOrCreateKey($user->id, $request->receiver_id);
            $key = $encryptionKey->getDecryptedKey();

            $encrypted = $encryptionService->encrypt($request->input('content'), $key);
            $encryptedContent = $encrypted['iv'] . ':' . $encrypted['encrypted'];
            $encryptionKeyId = $encryptionKey->key_id;
            $isEncrypted = true;
        }

        $data = [
            'organization_id' => $user->organization_id,
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'channel_id' => $request->channel_id,
            'content' => $isEncrypted ? null : $request->input('content'), // Contenu en clair seulement si non crypté
            'encrypted_content' => $encryptedContent,
            'encryption_key_id' => $encryptionKeyId,
            'is_encrypted' => $isEncrypted,
            'type' => $request->type ?? 'text',
            'is_read' => false,
        ];

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message-attachments', 'public');
                $attachmentPaths[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
            $data['attachments'] = $attachmentPaths;
        }

        $message = Message::create($data);

        // Log activity
        $recipient = $request->channel_id
            ? "canal #{$request->channel_id}"
            : "utilisateur #{$request->receiver_id}";
        ActivityLog::log('message_sent', "Message envoyé à {$recipient}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Message envoyé avec succès.',
                'data' => $message->load(['sender', 'receiver', 'channel'])
            ], 201);
        }

        return redirect()->back()->with('success', 'Message envoyé avec succès.');
    }

    /**
     * Display the specified message.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $message = Message::with(['sender', 'receiver', 'channel', 'organization'])
            ->findOrFail($id);

        // Check access
        $hasAccess = $user->isOwner()
            || $message->sender_id === $user->id
            || $message->receiver_id === $user->id
            || ($message->channel_id && $message->channel->users()->where('users.id', $user->id)->exists());

        if (!$hasAccess) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Mark as read if user is the receiver
        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['data' => $message], 200);
        }

        return view('messages.show', ['message' => $message]);
    }

    /**
     * Update the specified message (edit content).
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $message = Message::findOrFail($id);

        // Only sender can edit
        if ($message->sender_id !== $user->id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Seul l\'expéditeur peut modifier ce message.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $message->update([
            'content' => $request->input('content'),
        ]);

        // Log activity
        ActivityLog::log('message_updated', "Message modifié (ID: {$message->id})");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Message modifié avec succès.',
                'data' => $message
            ], 200);
        }

        return redirect()->back()->with('success', 'Message modifié avec succès.');
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $message = Message::findOrFail($id);

        // Only sender can delete
        if ($message->sender_id !== $user->id && !$user->isOwner()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Delete attachments if exist
        if ($message->attachments && is_array($message->attachments)) {
            foreach ($message->attachments as $attachment) {
                if (isset($attachment['path']) && Storage::disk('public')->exists($attachment['path'])) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }

        $message->delete();

        // Log activity
        ActivityLog::log('message_deleted', "Message supprimé (ID: {$id})");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Message supprimé avec succès.'], 200);
        }

        return redirect()->back()->with('success', 'Message supprimé avec succès.');
    }

    /**
     * Mark message(s) as read.
     */
    public function markAsRead(Request $request, $id = null)
    {
        $user = $request->user();

        if ($id) {
            // Mark single message as read
            $message = Message::findOrFail($id);

            if ($message->receiver_id !== $user->id) {
                return $request->wantsJson()
                    ? response()->json(['message' => 'Accès non autorisé.'], 403)
                    : redirect()->back()->with('error', 'Accès non autorisé.');
            }

            $message->markAsRead();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Message marqué comme lu.'], 200);
            }
        } else {
            // Mark multiple messages as read
            $v = Validator::make($request->all(), [
                'message_ids' => 'required|array',
                'message_ids.*' => 'exists:messages,id',
            ]);

            if ($v->fails()) {
                if ($request->wantsJson()) {
                    return response()->json(['errors' => $v->errors()], 422);
                }
                return redirect()->back()->withErrors($v);
            }

            Message::whereIn('id', $request->message_ids)
                ->where('receiver_id', $user->id)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Messages marqués comme lus.'], 200);
            }
        }

        return redirect()->back()->with('success', 'Message(s) marqué(s) comme lu(s).');
    }

    /**
     * Mark all messages as read for current user.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Log activity
        ActivityLog::log('messages_marked_read', 'Tous les messages marqués comme lus');

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Tous les messages marqués comme lus.'], 200);
        }

        return redirect()->back()->with('success', 'Tous les messages marqués comme lus.');
    }

    /**
     * Get unread messages count.
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $count = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        if ($request->wantsJson()) {
            return response()->json(['count' => $count], 200);
        }

        return response()->json(['count' => $count], 200);
    }

    /**
     * Search messages.
     */
    public function search(Request $request)
    {
        $user = $request->user();

        $v = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v);
        }

        $searchQuery = $request->query;

        $messages = Message::where('organization_id', $user->organization_id)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id)
                    ->orWhereHas('channel.users', function ($subQ) use ($user) {
                        $subQ->where('users.id', $user->id);
                    });
            })
            ->where('content', 'LIKE', "%{$searchQuery}%")
            ->with(['sender', 'receiver', 'channel'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($messages, 200);
        }

        return view('messages.search', [
            'messages' => $messages,
            'query' => $searchQuery
        ]);
    }
}

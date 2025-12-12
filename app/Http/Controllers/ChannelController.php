<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelMessage;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChannelController extends Controller
{
    /**
     * Display a listing of channels.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Channel::with(['organization', 'department', 'creator', 'users'])
            ->withCount(['users as members_count', 'messages as messages_count']);

        // Owner/Admin voient tout
        if ($user->isOwner() || $user->isAdmin() || $user->isSuperAdmin()) {
            $query->where('organization_id', $user->organization_id);
        } else {
            // Utilisateur normal : canaux publics, département, ou où il est membre
            $query->where(function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id)
                  ->where(function ($sub) use ($user) {
                      $sub->where('type', 'public')
                          ->orWhere(function ($d) use ($user) {
                              $d->where('type', 'department')
                                ->where('department_id', $user->department_id);
                          })
                          ->orWhereHas('users', function ($m) use ($user) {
                              $m->where('users.id', $user->id);
                          });
                  });
            });
        }

        $channels = $query->orderBy('name')->get();

        // Add is_member flag
        $channels->each(function ($channel) use ($user) {
            $channel->is_member = $channel->users->contains('id', $user->id);
        });

        if ($request->wantsJson()) {
            return response()->json(['data' => $channels], 200);
        }

        return view('channels.index', ['channels' => $channels]);
    }

    /**
     * Store a newly created channel.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'organization_id' => 'required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255|unique:channels,name,NULL,id,organization_id,' . $request->organization_id,
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:public,private,department',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();

        // Force organization_id for non-owners
        if (!$user->isOwner()) {
            $data['organization_id'] = $user->organization_id;
        }

        // Validate department for department channels
        if ($data['type'] === 'department' && empty($data['department_id'])) {
            return $request->wantsJson()
                ? response()->json(['errors' => ['department_id' => ['Le département est requis pour les canaux départementaux.']]], 422)
                : redirect()->back()->withErrors(['department_id' => 'Le département est requis pour les canaux départementaux.'])->withInput();
        }

        $data['created_by'] = $user->id;

        $channel = Channel::create($data);

        // Add creator as member
        $channel->addMember($user);

        // Add selected users for private channels
        if ($data['type'] === 'private' && !empty($data['user_ids'])) {
            foreach ($data['user_ids'] as $userId) {
                $member = \App\Models\User::find($userId);
                if ($member) {
                    $channel->addMember($member);
                }
            }
        }

        // Diffuser l'événement de création du canal
        broadcast(new \App\Events\ChannelUpdated($channel, 'created', $user))->toOthers();

        ActivityLog::log('channel_created', "Canal créé: {$channel->display_name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Canal créé avec succès.',
                'data' => $channel->load(['organization', 'department', 'users']),
                'toast' => [
                    'type' => 'success',
                    'title' => 'Succès',
                    'message' => 'Canal créé avec succès.'
                ]
            ], 201);
        }

        return redirect()->route('channels.show', $channel->id)->with('success', 'Canal créé avec succès.');
    }

    /**
     * Display the specified channel.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $channel = Channel::with(['organization', 'department', 'creator', 'users'])
            ->withCount(['users as members_count'])
            ->findOrFail($id);

        // Check access
        if (!$user->isSuperAdmin() && $user->organization_id !== $channel->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $channel->is_member = $channel->users->contains('id', $user->id);

        if ($request->wantsJson()) {
            return response()->json(['data' => $channel], 200);
        }

        // Rétablir l'ancienne vue d'information du channel
        return view('channels.show', ['channel' => $channel]);
    }

    /**
     * Update the specified channel.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $channel = Channel::findOrFail($id);

        // Check access
        if (!$user->canManageUsers() || (!$user->isOwner() && $user->organization_id !== $channel->organization_id)) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'sometimes|in:public,private,department',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $channel->update($v->validated());

        // Diffuser l'événement de mise à jour
        broadcast(new \App\Events\ChannelUpdated($channel, 'updated', $user))->toOthers();

        ActivityLog::log('channel_updated', "Canal mis à jour: {$channel->name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Canal mis à jour avec succès.',
                'data' => $channel,
                'toast' => [
                    'type' => 'success',
                    'title' => 'Succès',
                    'message' => 'Canal mis à jour avec succès.'
                ]
            ], 200);
        }

        return redirect()->route('channels.show', $channel->id)->with('success', 'Canal mis à jour.');
    }

    /**
     * Remove the specified channel from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $channel = Channel::findOrFail($id);

        // Check access
        if (!$user->canManageUsers() || (!$user->isOwner() && $user->id !== $channel->created_by)) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $channelName = $channel->name;
        $channel->delete();

        ActivityLog::log('channel_deleted', "Canal supprimé: {$channelName}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Canal supprimé avec succès.'], 200);
        }

        return redirect()->route('channels.index')->with('success', 'Canal supprimé avec succès.');
    }

    /**
     * Join a channel.
     */
    public function join(Request $request, $id)
    {
        $user = $request->user();
        $channel = Channel::findOrFail($id);

        // Check if user can join this channel
        if (!$channel->canUserJoin($user)) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Vous ne pouvez pas rejoindre ce canal.'], 403)
                : redirect()->back()->with('error', 'Vous ne pouvez pas rejoindre ce canal.');
        }

        // Check if already member
        if ($channel->isMember($user)) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Vous êtes déjà membre de ce canal.'], 400)
                : redirect()->back()->with('error', 'Vous êtes déjà membre de ce canal.');
        }

        $channel->addMember($user);

        // Diffuser l'événement
        broadcast(new \App\Events\ChannelUpdated($channel, 'member_joined', $user))->toOthers();

        ActivityLog::log('channel_joined', "Rejoint le canal: {$channel->display_name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Vous avez rejoint le canal.',
                'toast' => [
                    'type' => 'success',
                    'title' => 'Bienvenue !',
                    'message' => "Vous avez rejoint le canal {$channel->name}"
                ]
            ], 200);
        }

        return redirect()->back()->with('success', 'Vous avez rejoint le canal.');
    }

    /**
     * Leave a channel.
     */
    public function leave(Request $request, $id)
    {
        $user = $request->user();
        $channel = Channel::findOrFail($id);

        // Check if member
        if (!$channel->isMember($user)) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 400)
                : redirect()->back()->with('error', 'Vous n\'êtes pas membre de ce canal.');
        }

        // Prevent leaving if user is the only member and creator
        if ($channel->users()->count() === 1 && $channel->created_by === $user->id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Vous ne pouvez pas quitter un canal dont vous êtes le seul membre.'], 400)
                : redirect()->back()->with('error', 'Vous ne pouvez pas quitter un canal dont vous êtes le seul membre.');
        }

        // Diffuser l'événement avant de retirer le membre
        broadcast(new \App\Events\ChannelUpdated($channel, 'member_left', $user))->toOthers();

        $channel->removeMember($user);
        ActivityLog::log('channel_left', "Quitté le canal: {$channel->display_name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Vous avez quitté le canal.',
                'toast' => [
                    'type' => 'info',
                    'title' => 'Au revoir',
                    'message' => "Vous avez quitté le canal {$channel->name}"
                ]
            ], 200);
        }

        return redirect()->route('channels.index')->with('success', 'Vous avez quitté le canal.');
    }

    /**
     * Add members to channel.
     */
    public function addMembers(Request $request, $id)
    {
        $user = $request->user();
        $channel = Channel::findOrFail($id);

        // Check access
        if (!$user->canManageUsers() && $user->id !== $channel->created_by) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v);
        }

        $channel->users()->syncWithoutDetaching($request->user_ids);

        ActivityLog::log('channel_members_added', "Membres ajoutés au canal: {$channel->name}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Membres ajoutés avec succès.'], 200);
        }

        return redirect()->back()->with('success', 'Membres ajoutés avec succès.');
    }

    /**
     * Remove member from channel.
     */
    public function removeMember(Request $request, $id, $userId)
    {
        $user = $request->user();
        $channel = Channel::findOrFail($id);

        // Check access
        if (!$user->canManageUsers() && $user->id !== $channel->created_by) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $channel->users()->detach($userId);

        ActivityLog::log('channel_member_removed', "Membre retiré du canal: {$channel->name}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Membre retiré avec succès.'], 200);
        }

        return redirect()->back()->with('success', 'Membre retiré avec succès.');
    }

    /**
     * Récupérer les messages d'un canal
     */
    public function getMessages(Request $request, $channelId)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($channelId);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        // Récupérer les messages avec pagination
        $messages = $channel->messages()
            ->with(['sender', 'attachments', 'replyTo.sender'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50));

        // Formater les messages pour la réponse
        $formattedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'created_at' => $message->created_at,
                'attachments' => $message->attachments,
                'reply_to' => $message->reply_to,
                'reply_message' => $message->replyTo,
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

    /**
     * Envoyer un message dans un canal
     */
    public function sendMessage(Request $request)
    {
        $user = $request->user();

        $v = Validator::make($request->all(), [
            'channel_id' => 'required|exists:channels,id',
            'content' => 'required|string|max:5000',
            'type' => 'in:text,file,voice',
            'reply_to' => 'nullable|exists:messages,id',
            'attachment_ids' => 'nullable|array',
            'attachment_ids.*' => 'exists:message_attachments,id',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = $v->validated();

        // Vérifier que l'utilisateur est membre du canal
        $channel = Channel::findOrFail($data['channel_id']);
        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce canal.'], 403);
        }

        // Créer le message
        $message = ChannelMessage::create([
            'channel_id' => $channel->id,
            'user_id' => $user->id,
            'content' => $data['content'],
            'type' => $data['type'] ?? 'text',
            'reply_to' => $data['reply_to'] ?? null,
        ]);

        // Attacher les fichiers si présents
        if (!empty($data['attachment_ids'])) {
            $message->attachments = $data['attachment_ids'];
            $message->save();
        }

        // Charger les relations pour la réponse
        $message->load(['user', 'replyTo.user']);

        // Diffuser le message via WebSocket
        broadcast(new \App\Events\ChannelMessageSent($message))->toOthers();

        ActivityLog::log('channel_message_sent', "Message envoyé dans le canal: {$channel->name}");

        return response()->json([
            'message' => 'Message envoyé avec succès.',
            'data' => $message
        ], 201);
    }
}

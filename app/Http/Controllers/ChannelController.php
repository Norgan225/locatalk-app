<?php

namespace App\Http\Controllers;

use App\Models\Channel;
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

        // Filter by organization (all users see only their org's channels, except super_admin)
        if ($user && !$user->isSuperAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        // Optional filters
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by user membership
        if ($request->boolean('my_channels')) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
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
            'name' => 'required|string|max:255',
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

        // If not owner, force organization_id
        if (!$user->isOwner()) {
            $data['organization_id'] = $user->organization_id;
        }

        $data['created_by'] = $user->id;

        $channel = Channel::create($data);

        // Attach users to channel
        $userIds = $request->input('user_ids', []);
        if (!empty($userIds)) {
            $channel->users()->sync($userIds);
        }

        // Add creator as member
        $channel->users()->syncWithoutDetaching([$user->id]);

        ActivityLog::log('channel_created', "Canal créé: {$channel->name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Canal créé avec succès.',
                'data' => $channel->load(['organization', 'department', 'users'])
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
        $channel = Channel::with(['organization', 'department', 'creator', 'users', 'messages.sender'])
            ->withCount(['users as members_count', 'messages as messages_count'])
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

        ActivityLog::log('channel_updated', "Canal mis à jour: {$channel->name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Canal mis à jour avec succès.',
                'data' => $channel
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

        // Check if public or user has access
        if ($channel->type === 'private' && !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Canal privé. Accès sur invitation uniquement.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        if (!$channel->users()->where('users.id', $user->id)->exists()) {
            $channel->users()->attach($user->id);
            ActivityLog::log('channel_joined', "Rejoint le canal: {$channel->name}");
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Vous avez rejoint le canal.'], 200);
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

        $channel->users()->detach($user->id);

        ActivityLog::log('channel_left', "Quitté le canal: {$channel->name}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Vous avez quitté le canal.'], 200);
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
}

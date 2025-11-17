<?php

namespace App\Http\Controllers;

use App\Models\UserStatus;
use App\Models\User;
use App\Events\UserStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserStatusController extends Controller
{
    /**
     * Obtenir le statut de l'utilisateur connecté
     *
     * GET /api/status
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $status = UserStatus::getOrCreateForUser($user->id);

        return response()->json([
            'status' => $status->getVisibleStatus(),
            'status_details' => $status->getStatusWithColor(),
            'last_activity' => $status->last_activity?->toIso8601String(),
            'last_seen' => $status->last_seen?->toIso8601String(),
            'is_invisible' => $status->is_invisible,
            'device_type' => $status->device_type,
        ]);
    }

    /**
     * Mettre à jour le statut de l'utilisateur
     *
     * PUT /api/status
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:online,offline,away,busy,do_not_disturb',
            'custom_message' => 'nullable|string|max:255',
            'is_invisible' => 'nullable|boolean',
            'device_type' => 'nullable|string|in:desktop,mobile,web',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $status = UserStatus::updateOrCreateForUser($user->id, [
            'status' => $request->status,
            'custom_message' => $request->custom_message,
            'is_invisible' => $request->is_invisible ?? false,
            'device_type' => $request->device_type ?? 'web',
            'last_activity' => now(),
        ]);

        // Broadcast le changement
        broadcast(new UserStatusChanged($status))->toOthers();

        return response()->json([
            'message' => 'Statut mis à jour',
            'status' => $status->getStatusWithColor(),
        ]);
    }

    /**
     * Mettre l'utilisateur en ligne
     *
     * POST /api/status/online
     */
    public function setOnline(Request $request)
    {
        $user = $request->user();
        $deviceType = $request->input('device_type', 'web');

        $status = UserStatus::getOrCreateForUser($user->id);
        $status->setOnline($deviceType);

        broadcast(new UserStatusChanged($status))->toOthers();

        return response()->json([
            'message' => 'Vous êtes maintenant en ligne',
            'status' => $status->getStatusWithColor(),
        ]);
    }

    /**
     * Mettre l'utilisateur hors ligne
     *
     * POST /api/status/offline
     */
    public function setOffline(Request $request)
    {
        $user = $request->user();
        $status = UserStatus::getOrCreateForUser($user->id);
        $status->setOffline();

        broadcast(new UserStatusChanged($status))->toOthers();

        return response()->json([
            'message' => 'Vous êtes maintenant hors ligne',
        ]);
    }

    /**
     * Mettre l'utilisateur en mode absent
     *
     * POST /api/status/away
     */
    public function setAway(Request $request)
    {
        $user = $request->user();
        $status = UserStatus::getOrCreateForUser($user->id);
        $status->setAway();

        broadcast(new UserStatusChanged($status))->toOthers();

        return response()->json([
            'message' => 'Vous êtes en mode absent',
            'status' => $status->getStatusWithColor(),
        ]);
    }

    /**
     * Mettre l'utilisateur en mode occupé
     *
     * POST /api/status/busy
     */
    public function setBusy(Request $request)
    {
        $customMessage = $request->input('custom_message');

        $user = $request->user();
        $status = UserStatus::getOrCreateForUser($user->id);
        $status->setBusy($customMessage);

        broadcast(new UserStatusChanged($status))->toOthers();

        return response()->json([
            'message' => 'Vous êtes en mode occupé',
            'status' => $status->getStatusWithColor(),
        ]);
    }

    /**
     * Activer/désactiver le mode invisible
     *
     * POST /api/status/invisible
     */
    public function toggleInvisible(Request $request)
    {
        $invisible = $request->input('invisible', true);

        $user = $request->user();
        $status = UserStatus::getOrCreateForUser($user->id);
        $status->setInvisible($invisible);

        broadcast(new UserStatusChanged($status))->toOthers();

        return response()->json([
            'message' => $invisible ? 'Mode invisible activé' : 'Mode invisible désactivé',
            'is_invisible' => $status->is_invisible,
        ]);
    }

    /**
     * Ping pour maintenir l'activité (heartbeat)
     *
     * POST /api/status/ping
     */
    public function ping(Request $request)
    {
        $user = $request->user();
        $status = UserStatus::getOrCreateForUser($user->id);

        // Si l'utilisateur était offline, le remettre online
        if ($status->status === 'offline') {
            $status->setOnline($request->input('device_type', 'web'));
            broadcast(new UserStatusChanged($status))->toOthers();
        } else {
            $status->updateActivity();
        }

        return response()->json([
            'status' => 'ok',
            'last_activity' => $status->last_activity->toIso8601String(),
        ]);
    }

    /**
     * Obtenir le statut d'un utilisateur spécifique
     *
     * GET /api/status/user/{userId}
     */
    public function getUserStatus(Request $request, int $userId)
    {
        $status = UserStatus::where('user_id', $userId)->first();

        if (!$status) {
            return response()->json([
                'status' => 'offline',
                'message' => 'Aucun statut trouvé',
            ], 404);
        }

        return response()->json([
            'user_id' => $userId,
            'status' => $status->getVisibleStatus(),
            'status_details' => $status->getStatusWithColor(),
            'last_seen' => $status->last_seen?->toIso8601String(),
            'time_since_activity' => $status->getTimeSinceActivity(),
        ]);
    }

    /**
     * Obtenir les statuts de plusieurs utilisateurs
     *
     * POST /api/status/bulk
     */
    public function bulkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $statuses = UserStatus::whereIn('user_id', $request->user_ids)
            ->with('user:id,name,avatar')
            ->get()
            ->map(function ($status) {
                return [
                    'user_id' => $status->user_id,
                    'user_name' => $status->user->name,
                    'user_avatar' => $status->user->avatar,
                    'status' => $status->getVisibleStatus(),
                    'status_details' => $status->getStatusWithColor(),
                    'last_seen' => $status->last_seen?->toIso8601String(),
                    'is_online' => $status->isOnline(),
                ];
            });

        return response()->json([
            'statuses' => $statuses,
        ]);
    }

    /**
     * Obtenir tous les utilisateurs en ligne
     *
     * GET /api/status/online
     */
    public function getOnlineUsers(Request $request)
    {
        $onlineUsers = UserStatus::getOnlineUsers()
            ->map(function ($status) {
                return [
                    'user_id' => $status->user_id,
                    'user_name' => $status->user->name,
                    'user_avatar' => $status->user->avatar,
                    'status' => $status->getVisibleStatus(),
                    'status_details' => $status->getStatusWithColor(),
                    'device_type' => $status->device_type,
                ];
            });

        return response()->json([
            'online_count' => $onlineUsers->count(),
            'users' => $onlineUsers,
        ]);
    }
}

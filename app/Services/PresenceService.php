<?php

namespace App\Services;

use App\Models\UserStatus;
use App\Events\UserStatusChanged;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PresenceService
{
    /**
     * Durée d'inactivité avant passage en "away" (en minutes)
     */
    const AWAY_THRESHOLD = 10;

    /**
     * Durée d'inactivité avant passage en "offline" (en minutes)
     */
    const OFFLINE_THRESHOLD = 30;

    /**
     * Vérifier et mettre à jour les statuts des utilisateurs inactifs
     */
    public function checkInactiveUsers(): void
    {
        $now = Carbon::now();

        // Utilisateurs à passer en "away"
        $awayUsers = UserStatus::where('status', 'online')
            ->where('last_activity', '<=', $now->copy()->subMinutes(self::AWAY_THRESHOLD))
            ->where('last_activity', '>', $now->copy()->subMinutes(self::OFFLINE_THRESHOLD))
            ->get();

        foreach ($awayUsers as $status) {
            $status->setAway();
            broadcast(new UserStatusChanged($status))->toOthers();
            Log::info("User {$status->user_id} automatically set to away");
        }

        // Utilisateurs à passer en "offline"
        $offlineUsers = UserStatus::whereIn('status', ['online', 'away', 'busy'])
            ->where('last_activity', '<=', $now->copy()->subMinutes(self::OFFLINE_THRESHOLD))
            ->get();

        foreach ($offlineUsers as $status) {
            $status->setOffline();
            broadcast(new UserStatusChanged($status))->toOthers();
            Log::info("User {$status->user_id} automatically set to offline");
        }
    }

    /**
     * Obtenir le nombre d'utilisateurs en ligne
     */
    public function getOnlineCount(): int
    {
        return Cache::remember('online_users_count', 60, function () {
            return UserStatus::online()->recentlyActive(5)->count();
        });
    }

    /**
     * Obtenir la liste des utilisateurs en ligne
     */
    public function getOnlineUsersList(): array
    {
        return Cache::remember('online_users_list', 60, function () {
            return UserStatus::online()
                ->recentlyActive(5)
                ->with('user:id,name,avatar')
                ->get()
                ->map(function ($status) {
                    return [
                        'user_id' => $status->user_id,
                        'name' => $status->user->name,
                        'avatar' => $status->user->avatar,
                        'status_details' => $status->getStatusWithColor(),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Invalider le cache des utilisateurs en ligne
     */
    public function invalidateOnlineCache(): void
    {
        Cache::forget('online_users_count');
        Cache::forget('online_users_list');
    }

    /**
     * Définir un utilisateur comme en ligne
     */
    public function setUserOnline(int $userId, string $deviceType = 'web'): UserStatus
    {
        $status = UserStatus::getOrCreateForUser($userId);
        $status->setOnline($deviceType);

        $this->invalidateOnlineCache();
        broadcast(new UserStatusChanged($status))->toOthers();

        return $status;
    }

    /**
     * Définir un utilisateur comme hors ligne
     */
    public function setUserOffline(int $userId): UserStatus
    {
        $status = UserStatus::getOrCreateForUser($userId);
        $status->setOffline();

        $this->invalidateOnlineCache();
        broadcast(new UserStatusChanged($status))->toOthers();

        return $status;
    }

    /**
     * Vérifier si un utilisateur est disponible pour recevoir des notifications
     */
    public function isUserAvailable(int $userId): bool
    {
        $status = UserStatus::where('user_id', $userId)->first();

        if (!$status) {
            return false;
        }

        // Ne pas déranger
        if ($status->status === 'do_not_disturb') {
            return false;
        }

        // Invisible
        if ($status->is_invisible) {
            return false;
        }

        // En ligne ou absent
        return in_array($status->status, ['online', 'away']);
    }

    /**
     * Obtenir les statistiques de présence
     */
    public function getPresenceStats(): array
    {
        $stats = UserStatus::selectRaw('status, COUNT(*) as count')
            ->where('is_invisible', false)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return [
            'online' => $stats['online'] ?? 0,
            'away' => $stats['away'] ?? 0,
            'busy' => $stats['busy'] ?? 0,
            'do_not_disturb' => $stats['do_not_disturb'] ?? 0,
            'offline' => $stats['offline'] ?? 0,
            'total_active' => ($stats['online'] ?? 0) + ($stats['away'] ?? 0) + ($stats['busy'] ?? 0),
        ];
    }

    /**
     * Nettoyer les anciens statuts (maintenance)
     */
    public function cleanupOldStatuses(int $days = 90): int
    {
        return UserStatus::where('status', 'offline')
            ->where('last_activity', '<=', Carbon::now()->subDays($days))
            ->delete();
    }
}

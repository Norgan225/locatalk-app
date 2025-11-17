<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserStatus;
use Carbon\Carbon;

class UpdateUserActivity
{
    /**
     * Handle an incoming request.
     * Met à jour automatiquement le last_activity de l'utilisateur authentifié
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();

            // Mettre à jour l'activité uniquement toutes les 60 secondes pour éviter trop de requêtes DB
            $cacheKey = "user_activity_updated_{$userId}";
            $lastUpdate = cache()->get($cacheKey);

            if (!$lastUpdate || Carbon::parse($lastUpdate)->addSeconds(60)->isPast()) {
                UserStatus::updateOrCreate(
                    ['user_id' => $userId],
                    [
                        'last_activity' => now(),
                        'status' => 'online', // Force le statut online si l'utilisateur fait des requêtes
                    ]
                );

                cache()->put($cacheKey, now()->toDateTimeString(), 120); // Cache 2 minutes
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserStatus;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $status = UserStatus::getOrCreateForUser($request->user()->id);

            // Mettre à jour l'activité
            $status->updateActivity();

            // Si l'utilisateur était offline, le remettre online automatiquement
            if ($status->status === 'offline') {
                $status->setOnline($request->header('X-Device-Type', 'web'));

                // Broadcast le changement
                broadcast(new \App\Events\UserStatusChanged($status))->toOthers();
            }
        }

        return $next($request);
    }
}

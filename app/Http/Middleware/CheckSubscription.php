<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Vérifier que l'abonnement de l'organisation est actif
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $organization = $user->organization;

        // Vérifier si l'abonnement est actif
        if ($organization->subscription_status !== 'active' &&
            $organization->subscription_status !== 'trial') {

            // Seul l'owner peut accéder pour renouveler
            if (!$user->isOwner()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'L\'abonnement de votre organisation a expiré. Contactez votre administrateur.',
                        'subscription_status' => $organization->subscription_status
                    ], 403);
                }

                return redirect()->route('subscription.expired');
            }
        }

        return $next($request);
    }
}

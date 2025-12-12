<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOwner
{
    /**
     * Vérifier que l'utilisateur est Owner
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isOwner()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Accès réservé au propriétaire de l\'organisation.'
                ], 403);
            }

            return redirect()->route('dashboard')->withErrors([
                'access' => 'Accès réservé au propriétaire.'
            ]);
        }

        return $next($request);
    }
}

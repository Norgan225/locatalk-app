<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Vérifier que l'utilisateur est Owner OU Admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->canManageUsers()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Accès réservé aux administrateurs.'
                ], 403);
            }

            return redirect()->route('dashboard')->withErrors([
                'access' => 'Accès réservé aux administrateurs.'
            ]);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: Route::middleware('role:owner,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Accès refusé. Permissions insuffisantes.',
                    'required_roles' => $roles,
                    'your_role' => $user->role
                ], 403);
            }

            return redirect()->route('dashboard')->withErrors([
                'access' => 'Vous n\'avez pas les permissions nécessaires.'
            ]);
        }

        return $next($request);
    }
}

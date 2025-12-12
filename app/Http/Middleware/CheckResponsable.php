<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckResponsable
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, ['owner', 'admin', 'responsable'])) {
            return redirect()->route('dashboard')->withErrors([
                'access' => 'Accès réservé aux responsables.'
            ]);
        }

        return $next($request);
    }
}

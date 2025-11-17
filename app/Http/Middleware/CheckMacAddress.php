<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LoginAttempt;

class CheckMacAddress
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // 1. Vérifier si l'organisation autorise la connexion à distance
        if ($user->organization->allow_remote_access) {
            return $next($request); // Pas de restriction
        }

        // 2. Générer le fingerprint de l'appareil
        $deviceFingerprint = $this->generateDeviceFingerprint($request);

        // 3. Vérifier si l'appareil est autorisé
        $device = $user->devices()
            ->where('device_fingerprint', $deviceFingerprint)
            ->where('is_authorized', true)
            ->first();

        if (!$device) {
            // Enregistrer la tentative bloquée
            LoginAttempt::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $request->ip(),
                'status' => 'blocked',
                'reason' => 'Appareil non autorisé',
                'device_info' => json_encode([
                    'user_agent' => $request->userAgent(),
                    'browser' => $this->getBrowser($request),
                    'os' => $this->getOS($request),
                ]),
                'attempted_at' => now(),
            ]);

            // Rediriger avec message d'erreur (pas de déconnexion car peut causer des erreurs avec Sanctum)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '⚠️ Connexion impossible en dehors du bureau. Contactez votre administrateur.',
                    'error' => 'unauthorized_device'
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'device' => '⚠️ Connexion impossible en dehors du bureau. Contactez votre administrateur.'
            ]);
        }

        // 4. Mettre à jour la dernière utilisation
        $device->update([
            'last_used_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return $next($request);
    }

    /**
     * Générer un fingerprint unique pour l'appareil
     */
    private function generateDeviceFingerprint(Request $request): string
    {
        // Utiliser IP + User-Agent + écran (si fourni via JS)
        $data = $request->ip() . '|' . $request->userAgent();

        // Si le fingerprint est envoyé depuis le client (via JS)
        if ($request->has('device_fingerprint')) {
            return $request->input('device_fingerprint');
        }

        return hash('sha256', $data);
    }

    /**
     * Déterminer le navigateur
     */
    private function getBrowser(Request $request): string
    {
        $agent = $request->userAgent();

        if (preg_match('/Edg/i', $agent)) return 'Edge';
        if (preg_match('/Chrome/i', $agent)) return 'Chrome';
        if (preg_match('/Firefox/i', $agent)) return 'Firefox';
        if (preg_match('/Safari/i', $agent)) return 'Safari';
        if (preg_match('/Opera|OPR/i', $agent)) return 'Opera';

        return 'Unknown';
    }

    /**
     * Déterminer le système d'exploitation
     */
    private function getOS(Request $request): string
    {
        $agent = $request->userAgent();

        if (preg_match('/Windows NT 10/i', $agent)) return 'Windows 10/11';
        if (preg_match('/Windows/i', $agent)) return 'Windows';
        if (preg_match('/Mac OS X/i', $agent)) return 'macOS';
        if (preg_match('/Linux/i', $agent)) return 'Linux';
        if (preg_match('/Android/i', $agent)) return 'Android';
        if (preg_match('/iOS|iPhone|iPad/i', $agent)) return 'iOS';

        return 'Unknown';
    }
}

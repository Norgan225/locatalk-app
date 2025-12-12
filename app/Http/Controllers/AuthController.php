<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\LoginAttempt;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Connexion utilisateur",
     *     description="Authentification avec email, mot de passe et informations de l'appareil",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","device_name","mac_address"},
     *             @OA\Property(property="email", type="string", format="email", example="marie@apec.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="device_name", type="string", example="Chrome Browser"),
     *             @OA\Property(property="mac_address", type="string", example="00:11:22:33:44:55")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Connexion réussie"),
     *             @OA\Property(property="token", type="string", example="1|abcd1234..."),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Identifiants invalides"),
     *     @OA\Response(response=429, description="Trop de tentatives")
     * )
     *
     * Connexion utilisateur avec vérification device
     */
    public function login(Request $request)
    {
        // 1. Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // 2. Rate Limiting (protection contre brute force)
        $key = 'login-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->errorResponse(
                "Trop de tentatives. Réessayez dans {$seconds} secondes.",
                429
            );
        }

        // 3. Trouver l'utilisateur
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); // Bloquer pendant 60 secondes après 5 tentatives

            // Enregistrer la tentative échouée
            LoginAttempt::create([
                'user_id' => $user?->id,
                'email' => $request->email,
                'device_fingerprint' => $this->getDeviceFingerprint($request),
                'ip_address' => $request->ip(),
                'status' => 'failed',
                'reason' => 'Identifiants invalides',
                'device_info' => json_encode([
                    'user_agent' => $request->userAgent(),
                    'browser' => $this->getBrowser($request->userAgent()),
                    'os' => $this->getOS($request->userAgent()),
                ]),
                'attempted_at' => now(),
            ]);

            return $this->errorResponse('Identifiants invalides', 401);
        }

        // 4. Vérifier le statut de l'utilisateur
        if ($user->status !== 'active') {
            return $this->errorResponse('Compte ' . $user->status . '. Contactez votre administrateur.', 403);
        }

        // 5. Vérifier l'abonnement de l'organisation
        if (!in_array($user->organization->subscription_status, ['active', 'trial'])) {
            return $this->errorResponse(
                'L\'abonnement de votre organisation a expiré. Contactez votre administrateur.',
                403
            );
        }

        // 6. Générer le fingerprint de l'appareil
        $deviceFingerprint = $this->getDeviceFingerprint($request);

        // 7. Vérifier si l'organisation autorise la connexion à distance
        $allowRemoteAccess = $user->organization->allow_remote_access;

        // 8. Vérifier ou créer le device
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->first();

        if (!$device) {
            // Premier login depuis cet appareil
            $device = UserDevice::create([
                'user_id' => $user->id,
                'device_fingerprint' => $deviceFingerprint,
                'device_name' => $this->getDeviceName($request),
                'ip_address' => $request->ip(),
                'browser' => $this->getBrowser($request->userAgent()),
                'os' => $this->getOS($request->userAgent()),
                'user_agent' => $request->userAgent(),
                'is_authorized' => $allowRemoteAccess, // Autoriser automatiquement si connexion à distance activée
                'first_login_at' => now(),
                'last_used_at' => now(),
            ]);

            // Si connexion à distance désactivée, notifier l'owner
            if (!$allowRemoteAccess) {
                $this->notifyOwnerNewDevice($user, $device);
            }
        }

        // 9. Si l'appareil n'est pas autorisé
        if (!$device->is_authorized && !$allowRemoteAccess) {
            LoginAttempt::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $request->ip(),
                'status' => 'blocked',
                'reason' => 'Appareil non autorisé',
                'device_info' => json_encode([
                    'user_agent' => $request->userAgent(),
                    'browser' => $this->getBrowser($request->userAgent()),
                    'os' => $this->getOS($request->userAgent()),
                ]),
                'attempted_at' => now(),
            ]);

            return $this->errorResponse(
                '⚠️ Connexion impossible en dehors du bureau. Contactez votre administrateur.',
                403
            );
        }

        // 10. Mettre à jour le device
        $device->update([
            'last_used_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        // 11. Authentifier l'utilisateur
        Auth::login($user);

        // 12. Mettre à jour les informations de connexion
        $user->update([
            'last_login_at' => now(),
            'last_ip_address' => $request->ip(),
        ]);

        // 13. Enregistrer l'activité
        ActivityLog::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'action' => 'login',
            'description' => 'Connexion réussie',
            'ip_address' => $request->ip(),
            'device_fingerprint' => $deviceFingerprint,
            'metadata' => [
                'browser' => $this->getBrowser($request->userAgent()),
                'os' => $this->getOS($request->userAgent()),
            ],
        ]);

        // 14. Enregistrer tentative réussie
        LoginAttempt::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'device_fingerprint' => $deviceFingerprint,
            'ip_address' => $request->ip(),
            'status' => 'success',
            'reason' => 'Connexion réussie',
            'attempted_at' => now(),
        ]);

        // 15. Réinitialiser le rate limiter
        RateLimiter::clear($key);

        // 16. Vérifier si changement de mot de passe requis
        $requirePasswordChange = !$user->password_changed && $user->temp_password;

        // 17. Générer le token (Sanctum)
        $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

        // 18. Charger les relations
        $user->load(['organization', 'department']);

        // 19. Retourner la réponse
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $requirePasswordChange
                    ? 'Première connexion. Veuillez changer votre mot de passe.'
                    : 'Connexion réussie',
                'require_password_change' => $requirePasswordChange,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'organization' => [
                        'id' => $user->organization->id,
                        'name' => $user->organization->name,
                        'plan' => $user->organization->plan,
                    ],
                    'department' => $user->department ? [
                        'id' => $user->department->id,
                        'name' => $user->department->name,
                        'color' => $user->department->color,
                    ] : null,
                ],
                'token' => $token,
            ]);
        }

        // Redirection Web
        if ($requirePasswordChange) {
            return redirect()->route('password.change')->with('info', 'Veuillez changer votre mot de passe.');
        }

        return redirect()->route('dashboard')->with('success', 'Connexion réussie !');
    }

    /**
     * Déconnexion utilisateur
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Mettre à jour last_logout_at
            $user->update(['last_logout_at' => now()]);

            // Logger l'activité
            ActivityLog::create([
                'user_id' => $user->id,
                'organization_id' => $user->organization_id,
                'action' => 'logout',
                'description' => 'Déconnexion',
                'ip_address' => $request->ip(),
                'device_fingerprint' => $this->getDeviceFingerprint($request),
            ]);

            // Révoquer le token actuel (Sanctum)
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            // Déconnecter (session web)
            Auth::logout();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ]);
        }

        return redirect()->route('login')->with('success', 'Déconnexion réussie');
    }

    /**
     * Changer le mot de passe (première connexion)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Vérifier le mot de passe actuel (temporaire ou ancien)
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Le mot de passe actuel est incorrect.'],
            ]);
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->new_password),
            'temp_password' => null,
            'password_changed' => true,
        ]);

        // Logger l'activité
        ActivityLog::log('password_changed', 'Mot de passe modifié');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Mot de passe modifié avec succès !');
    }

    /**
     * Afficher l'utilisateur connecté
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['organization', 'department', 'devices']);

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Révoquer tous les tokens (déconnexion de tous les appareils)
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();

        // Révoquer tous les tokens
        $user->tokens()->delete();

        // Logger
        ActivityLog::log('logout_all', 'Déconnexion de tous les appareils');

        return response()->json([
            'success' => true,
            'message' => 'Déconnecté de tous les appareils'
        ]);
    }

    // ========================================
    // MÉTHODES PRIVÉES
    // ========================================

    /**
     * Générer un fingerprint unique de l'appareil
     */
    private function getDeviceFingerprint(Request $request): string
    {
        // Si envoyé depuis le client (JS)
        if ($request->has('device_fingerprint')) {
            return $request->input('device_fingerprint');
        }

        // Sinon, générer à partir de IP + User-Agent
        return hash('sha256', $request->ip() . '|' . $request->userAgent());
    }

    /**
     * Obtenir un nom lisible pour l'appareil
     */
    private function getDeviceName(Request $request): string
    {
        $browser = $this->getBrowser($request->userAgent());
        $os = $this->getOS($request->userAgent());

        return "{$browser} sur {$os}";
    }

    /**
     * Déterminer le navigateur
     */
    private function getBrowser(string $userAgent): string
    {
        if (preg_match('/Edg/i', $userAgent)) return 'Edge';
        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Opera|OPR/i', $userAgent)) return 'Opera';

        return 'Navigateur inconnu';
    }

    /**
     * Déterminer le système d'exploitation
     */
    private function getOS(string $userAgent): string
    {
        if (preg_match('/Windows NT 10/i', $userAgent)) return 'Windows 10/11';
        if (preg_match('/Windows/i', $userAgent)) return 'Windows';
        if (preg_match('/Mac OS X/i', $userAgent)) return 'macOS';
        if (preg_match('/Linux/i', $userAgent)) return 'Linux';
        if (preg_match('/Android/i', $userAgent)) return 'Android';
        if (preg_match('/iOS|iPhone|iPad/i', $userAgent)) return 'iOS';

        return 'Système inconnu';
    }

    /**
     * Notifier l'owner d'un nouveau device
     */
    private function notifyOwnerNewDevice(User $user, UserDevice $device): void
    {
        $owner = $user->organization->users()->where('role', 'owner')->first();

        if ($owner) {
            \App\Models\Notification::create([
                'user_id' => $owner->id,
                'type' => 'new_device_detected',
                'title' => '🚨 Nouveau appareil détecté',
                'message' => "{$user->name} a tenté de se connecter depuis un nouvel appareil : {$device->device_name}",
                'data' => [
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'ip_address' => $device->ip_address,
                ],
                'action_url' => null,
            ]);
        }
    }

    /**
     * Retourner une réponse d'erreur
     */
    private function errorResponse(string $message, int $code = 400)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], $code);
        }

        return back()->withErrors(['error' => $message])->withInput();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\CallSessionKey;
use App\Models\CallParticipantKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CallEncryptionController extends Controller
{
    /**
     * Initialiser une session de cryptage pour un appel
     *
     * POST /api/calls/{callId}/encryption/init
     */
    public function initializeSession(Request $request, int $callId)
    {
        $user = $request->user();
        $call = Call::findOrFail($callId);

        // Vérifier que l'utilisateur est le caller ou un participant
        if ($call->caller_id !== $user->id) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        // Créer la session de cryptage
        $sessionKey = CallSessionKey::createForCall($call->id);

        // Ajouter automatiquement le caller
        $participantKey = $sessionKey->addParticipant($user->id);

        return response()->json([
            'session_id' => $sessionKey->session_id,
            'algorithm' => $sessionKey->algorithm,
            'participant_key' => $participantKey->getDecryptedKey(),
            'message' => 'Session de cryptage initialisée avec succès'
        ], 201);
    }

    /**
     * Rejoindre une session de cryptage (obtenir sa clé)
     *
     * POST /api/calls/{callId}/encryption/join
     */
    public function joinSession(Request $request, int $callId)
    {
        $user = $request->user();
        $call = Call::findOrFail($callId);

        $sessionKey = $call->sessionKey;

        if (!$sessionKey || !$sessionKey->is_active) {
            return response()->json([
                'message' => 'Aucune session de cryptage active pour cet appel'
            ], 404);
        }

        // Vérifier si déjà participant
        $existingKey = CallParticipantKey::where('call_session_key_id', $sessionKey->id)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        if ($existingKey) {
            return response()->json([
                'session_id' => $sessionKey->session_id,
                'participant_key' => $existingKey->getDecryptedKey(),
                'key_version' => $existingKey->key_version,
                'message' => 'Vous êtes déjà dans la session'
            ]);
        }

        // Ajouter le nouveau participant
        $participantKey = $sessionKey->addParticipant($user->id);

        return response()->json([
            'session_id' => $sessionKey->session_id,
            'algorithm' => $sessionKey->algorithm,
            'participant_key' => $participantKey->getDecryptedKey(),
            'key_version' => $participantKey->key_version,
            'message' => 'Vous avez rejoint la session cryptée'
        ], 200);
    }

    /**
     * Quitter une session de cryptage
     *
     * POST /api/calls/{callId}/encryption/leave
     */
    public function leaveSession(Request $request, int $callId)
    {
        $user = $request->user();
        $call = Call::findOrFail($callId);

        $sessionKey = $call->sessionKey;

        if (!$sessionKey) {
            return response()->json([
                'message' => 'Aucune session trouvée'
            ], 404);
        }

        $participantKey = CallParticipantKey::where('call_session_key_id', $sessionKey->id)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        if (!$participantKey) {
            return response()->json([
                'message' => 'Vous n\'êtes pas dans cette session'
            ], 404);
        }

        $participantKey->markAsLeft();

        return response()->json([
            'message' => 'Vous avez quitté la session cryptée'
        ], 200);
    }

    /**
     * Terminer une session de cryptage (fin d'appel)
     *
     * POST /api/calls/{callId}/encryption/end
     */
    public function endSession(Request $request, int $callId)
    {
        $user = $request->user();
        $call = Call::findOrFail($callId);

        // Seul le caller peut terminer la session
        if ($call->caller_id !== $user->id) {
            return response()->json([
                'message' => 'Seul l\'initiateur de l\'appel peut terminer la session'
            ], 403);
        }

        $sessionKey = $call->sessionKey;

        if (!$sessionKey) {
            return response()->json([
                'message' => 'Aucune session active'
            ], 404);
        }

        $sessionKey->deactivate();

        return response()->json([
            'message' => 'Session de cryptage terminée'
        ], 200);
    }

    /**
     * Rotation de la clé de session (pour sécurité renforcée)
     *
     * POST /api/calls/{callId}/encryption/rotate
     */
    public function rotateKey(Request $request, int $callId)
    {
        $user = $request->user();
        $call = Call::findOrFail($callId);

        if ($call->caller_id !== $user->id) {
            return response()->json([
                'message' => 'Seul l\'initiateur peut effectuer une rotation de clé'
            ], 403);
        }

        $sessionKey = $call->sessionKey;

        if (!$sessionKey || !$sessionKey->is_active) {
            return response()->json([
                'message' => 'Aucune session active'
            ], 404);
        }

        $sessionKey->rotateKey();

        // Récupérer les nouvelles clés de tous les participants
        $participantKeys = $sessionKey->participantKeys()
            ->whereNull('left_at')
            ->with('user')
            ->get()
            ->map(function ($pk) {
                return [
                    'user_id' => $pk->user_id,
                    'user_name' => $pk->user->name,
                    'new_key_version' => $pk->key_version,
                ];
            });

        return response()->json([
            'message' => 'Rotation de clé effectuée avec succès',
            'participants_updated' => $participantKeys,
        ], 200);
    }

    /**
     * Obtenir les informations de la session de cryptage
     *
     * GET /api/calls/{callId}/encryption/info
     */
    public function getSessionInfo(Request $request, int $callId)
    {
        $user = $request->user();
        $call = Call::findOrFail($callId);

        $sessionKey = $call->sessionKey;

        if (!$sessionKey) {
            return response()->json([
                'encrypted' => false,
                'message' => 'Cet appel n\'est pas crypté'
            ], 200);
        }

        $participants = $sessionKey->participantKeys()
            ->with('user')
            ->get()
            ->map(function ($pk) {
                return [
                    'user_id' => $pk->user_id,
                    'user_name' => $pk->user->name,
                    'joined_at' => $pk->joined_at->toIso8601String(),
                    'left_at' => $pk->left_at?->toIso8601String(),
                    'is_active' => $pk->isActive(),
                    'key_version' => $pk->key_version,
                ];
            });

        return response()->json([
            'encrypted' => true,
            'session_id' => $sessionKey->session_id,
            'algorithm' => $sessionKey->algorithm,
            'is_active' => $sessionKey->is_active,
            'created_at' => $sessionKey->created_at,
            'expires_at' => $sessionKey->expires_at,
            'participants' => $participants,
            'total_participants' => $participants->count(),
            'active_participants' => $participants->where('is_active', true)->count(),
        ], 200);
    }

    /**
     * Générer un nonce pour cryptage de paquets média
     *
     * POST /api/calls/{callId}/encryption/nonce
     */
    public function generateNonce(Request $request, int $callId)
    {
        $validator = Validator::make($request->all(), [
            'counter' => 'required|integer|min:0',
            'timestamp' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $encryptionService = app(\App\Services\EncryptionService::class);

        $nonce = $encryptionService->generateNonce(
            $request->counter,
            $request->timestamp
        );

        return response()->json([
            'nonce' => $nonce,
        ], 200);
    }
}

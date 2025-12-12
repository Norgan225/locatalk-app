<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Events\CallSignalEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallSignalController extends Controller
{
    /**
     * Envoyer un signal WebRTC via Reverb
     */
    public function sendSignal(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:call-offer,call-answer,ice-candidate,call-rejected,call-ended',
            'data' => 'required|array',
        ]);

        $type = $validated['type'];
        $data = $validated['data'];
        $user = $request->user();

        Log::info('Signal WebRTC reçu', [
            'type' => $type,
            'from' => $user->id,
            'data' => $data
        ]);

        // Déterminer le destinataire et enrichir les données
        $receiverId = null;
        $enrichedData = $data;

        if ($type === 'call-offer') {
            $receiverId = $data['receiverId'] ?? null;

            // Enrichir avec les infos de l'appel et de l'appelant
            if (isset($data['callId'])) {
                $call = Call::with(['caller'])->find($data['callId']);
                if ($call) {
                    $enrichedData['call'] = $call;
                    $enrichedData['caller'] = $call->caller;
                    $enrichedData['offer'] = $data['offer'];
                }
            }
        } elseif (isset($data['callId'])) {
            $call = Call::find($data['callId']);
            if ($call) {
                // Le destinataire est l'autre personne dans l'appel
                $receiverId = $call->caller_id === $user->id ? $call->receiver_id : $call->caller_id;
            }
        }

        if (!$receiverId) {
            return response()->json(['error' => 'Destinataire non trouvé'], 400);
        }

        // Broadcaster le signal au destinataire via Reverb
        try {
            broadcast(new CallSignalEvent($receiverId, $type, $enrichedData))->toOthers();

            Log::info('Signal diffusé', [
                'type' => $type,
                'to' => $receiverId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Signal envoyé'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur broadcast signal', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de l\'envoi du signal'
            ], 500);
        }
    }

    /**
     * Accepter un appel
     */
    public function acceptCall(Request $request, $callId)
    {
        $call = Call::findOrFail($callId);
        $user = $request->user();

        // Vérifier que l'utilisateur est le destinataire
        if ($call->receiver_id !== $user->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Valider la présence de la réponse WebRTC
        $validated = $request->validate([
            'answer' => 'required|array'
        ]);

        // Mettre à jour le statut
        $call->update(['status' => 'ongoing']);

        // Envoyer la réponse WebRTC à l'appelant
        broadcast(new CallSignalEvent($call->caller_id, 'call-answer', [
            'callId' => $callId,
            'answer' => $validated['answer']
        ]))->toOthers();

        Log::info('Appel accepté', [
            'call_id' => $callId,
            'receiver' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'call' => $call
        ]);
    }

    /**
     * Rejeter un appel
     */
    public function rejectCall(Request $request, $callId)
    {
        $call = Call::findOrFail($callId);
        $user = $request->user();

        // Vérifier que l'utilisateur est le destinataire
        if ($call->receiver_id !== $user->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Mettre à jour le statut
        $call->update([
            'status' => 'missed',
            'ended_at' => now()
        ]);

        // Notifier l'appelant
        broadcast(new CallSignalEvent($call->caller_id, 'call-rejected', [
            'callId' => $callId,
            'rejectedBy' => $user->id
        ]))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Appel rejeté'
        ]);
    }
}

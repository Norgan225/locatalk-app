<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CallController extends Controller
{
    /**
     * Display a listing of calls.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Call::with(['caller', 'receiver', 'channel']);

        // Filter by user involvement
        if ($request->boolean('my_calls')) {
            $query->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $calls = $query->orderByDesc('created_at')->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($calls, 200);
        }

        return view('calls.index', ['calls' => $calls]);
    }

    /**
     * Initiate a new call.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $v = Validator::make($request->all(), [
            'receiver_id' => 'required_without:channel_id|exists:users,id',
            'channel_id' => 'required_without:receiver_id|exists:channels,id',
            'type' => 'required|in:audio,video',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();
        $data['caller_id'] = $user->id;
        $data['organization_id'] = $user->organization_id;
        $data['status'] = 'outgoing';
        $data['started_at'] = now();

        // Generate Jitsi Link
        $roomName = 'locatalk-call-' . uniqid();
        $data['meeting_link'] = "https://meet.jit.si/{$roomName}";

        $call = Call::create($data);

        ActivityLog::log('call_initiated', "Appel initié: {$call->type}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Appel initié avec succès.',
                'data' => $call->load(['caller', 'receiver', 'channel'])
            ], 201);
        }

        return redirect()->route('web.calls.show', $call->id);
    }

    /**
     * Display the specified call.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $call = Call::with(['caller', 'receiver', 'channel'])->findOrFail($id);

        // Check access
        if ($user->id !== $call->caller_id && $user->id !== $call->receiver_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        if ($request->wantsJson()) {
            return response()->json($call);
        }

        $otherUser = $user->id === $call->caller_id ? $call->receiver : $call->caller;

        return view('calls.show', compact('call', 'otherUser'));
    }

    /**
     * Answer/join a call.
     */
    public function answer(Request $request, $id)
    {
        $user = $request->user();
        $call = Call::findOrFail($id);

        // Check if user is receiver
        if ($user->id !== $call->receiver_id && !$call->channel_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Vous ne pouvez pas répondre à cet appel.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        if ($call->status !== 'initiated') {
            return $request->wantsJson()
                ? response()->json(['message' => 'Cet appel ne peut pas être répondu.'], 400)
                : redirect()->back()->with('error', 'Appel non disponible.');
        }

        $call->update(['status' => 'ongoing']);

        ActivityLog::log('call_answered', "Appel répondu: {$call->id}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Appel en cours.',
                'data' => $call
            ], 200);
        }

        return redirect()->back()->with('success', 'Appel en cours.');
    }

    /**
     * End a call.
     */
    public function end(Request $request, $id)
    {
        $user = $request->user();
        $call = Call::findOrFail($id);

        // Check if user is involved
        if ($user->id !== $call->caller_id && $user->id !== $call->receiver_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        if (in_array($call->status, ['ended', 'missed', 'cancelled'])) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Cet appel est déjà terminé.'], 400)
                : redirect()->back()->with('error', 'Appel déjà terminé.');
        }

        $call->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        ActivityLog::log('call_ended', "Appel terminé: {$call->id}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Appel terminé.',
                'data' => $call
            ], 200);
        }

        return redirect()->route('calls.index')->with('success', 'Appel terminé.');
    }

    /**
     * Reject/cancel a call.
     */
    public function reject(Request $request, $id)
    {
        $user = $request->user();
        $call = Call::findOrFail($id);

        // Check if user is involved
        if ($user->id !== $call->caller_id && $user->id !== $call->receiver_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        if ($call->status !== 'initiated') {
            return $request->wantsJson()
                ? response()->json(['message' => 'Cet appel ne peut pas être rejeté.'], 400)
                : redirect()->back()->with('error', 'Action non disponible.');
        }

        $status = ($user->id === $call->caller_id) ? 'cancelled' : 'missed';

        $call->update([
            'status' => $status,
            'ended_at' => now(),
        ]);

        ActivityLog::log('call_rejected', "Appel rejeté: {$call->id}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Appel rejeté.',
                'data' => $call
            ], 200);
        }

        return redirect()->back()->with('success', 'Appel rejeté.');
    }

    /**
     * Get call history for current user.
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $calls = Call::with(['caller', 'receiver', 'channel'])
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->orderByDesc('created_at')
            ->paginate(50);

        if ($request->wantsJson()) {
            return response()->json($calls, 200);
        }

        return view('calls.history', ['calls' => $calls]);
    }
}

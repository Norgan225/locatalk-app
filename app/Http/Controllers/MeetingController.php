<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * Display a listing of meetings.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Meeting::with(['creator', 'participants']);

        // Filter: Show only meetings where user is creator or participant
        if (!$user->isSuperAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id) // Meetings created by user
                  ->orWhereHas('participants', function ($p) use ($user) {
                      $p->where('user_id', $user->id); // Meetings where user is participant
                  });
            })
            ->where('organization_id', $user->organization_id);
        } elseif ($request->has('organization_id') && $request->organization_id) {
            // Super admin can filter by organization
            $query->where('organization_id', $request->organization_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $date = Carbon::parse($request->date);
            $query->whereDate('start_time', $date);
        }

        $meetings = $query->orderBy('start_time', 'desc')->paginate(20);

        // Calculate stats (only for meetings user has access to)
        $statsQuery = Meeting::query();
        if (!$user->isSuperAdmin()) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('participants', function ($p) use ($user) {
                      $p->where('user_id', $user->id);
                  });
            })
            ->where('organization_id', $user->organization_id);
        } elseif ($request->has('organization_id') && $request->organization_id) {
            $statsQuery->where('organization_id', $request->organization_id);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'upcoming' => (clone $statsQuery)->where('status', 'scheduled')->where('start_time', '>', now())->count(),
            'ongoing' => (clone $statsQuery)->where('status', 'ongoing')->count(),
            'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
        ];

        // Get organizations for super admin
        $organizations = $user->isSuperAdmin() ? Organization::all() : collect();

        if ($request->wantsJson()) {
            return response()->json($meetings, 200);
        }

        return view('meetings.index', compact('meetings', 'stats', 'organizations'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create(Request $request)
    {
        $user = $request->user();

        // Super admin can add any user, others only from same organization
        if ($user->isSuperAdmin()) {
            $users = User::where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        } else {
            $users = User::where('organization_id', $user->organization_id)
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        }

        return view('meetings.create', compact('users'));
    }

    /**
     * Store a newly created meeting.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:5|max:480',
            'meeting_link' => 'nullable|url',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'is_recorded' => 'nullable|boolean',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();

        // Set organization
        $data['organization_id'] = $user->organization_id;
        $data['created_by'] = $user->id;
        $data['status'] = 'scheduled';

        // Calculate end_time
        $startTime = Carbon::parse($data['start_time']);
        $data['end_time'] = $startTime->copy()->addMinutes($data['duration']);
        unset($data['duration']);

        // Set is_recorded flag
        $data['is_recorded'] = $request->has('is_recorded');

        $meeting = Meeting::create($data);

        // Attach participants
        if ($request->has('participants')) {
            $meeting->participants()->attach($request->participants, [
                'joined_at' => null,
                'left_at' => null,
            ]);
        }

        ActivityLog::log('meeting_created', "Réunion créée: {$meeting->title}");

        return redirect()->route('web.meetings.show', $meeting->id)->with('success', 'Réunion créée avec succès.');
    }

    /**
     * Display the specified meeting.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::with(['creator', 'participants'])->findOrFail($id);

        // Check access: user must be creator, participant, or super admin
        $isCreator = $user->id === $meeting->created_by;
        $isParticipant = $meeting->participants()->where('user_id', $user->id)->exists();
        $isSuperAdmin = $user->isSuperAdmin();

        if (!$isCreator && !$isParticipant && !$isSuperAdmin) {
            return redirect()->route('web.meetings')->with('error', 'Vous n\'avez pas accès à cette réunion.');
        }

        // Check organization access
        if (!$isSuperAdmin && $user->organization_id !== $meeting->organization_id) {
            return redirect()->route('web.meetings')->with('error', 'Accès non autorisé.');
        }

        if ($request->wantsJson()) {
            return response()->json(['data' => $meeting], 200);
        }

        return view('meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified meeting.
     */
    public function edit(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::with(['creator', 'participants'])->findOrFail($id);

        // Check access (only creator or admin)
        if ($user->id !== $meeting->created_by && !$user->can('manage-users')) {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Super admin can add any user, others only from same organization as the meeting
        if ($user->isSuperAdmin()) {
            $users = User::where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        } else {
            $users = User::where('organization_id', $meeting->organization_id)
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        }

        return view('meetings.edit', compact('meeting', 'users'));
    }

    /**
     * Update the specified meeting.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        // Check access (only creator or admin)
        if ($user->id !== $meeting->created_by && !$user->can('manage-users')) {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'is_recorded' => 'nullable|boolean',
            'recording_url' => 'nullable|url',
            'ai_summary' => 'nullable|string',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();
        $data['is_recorded'] = $request->has('is_recorded');

        $meeting->update($data);

        // Update participants
        if ($request->has('participants')) {
            $meeting->participants()->sync($request->participants);
        }

        ActivityLog::log('meeting_updated', "Réunion mise à jour: {$meeting->title}");

        return redirect()->route('web.meetings.show', $meeting->id)->with('success', 'Réunion mise à jour avec succès.');
    }

    /**
     * Remove the specified meeting from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        // Check access (only creator or admin)
        if ($user->id !== $meeting->created_by && !$user->can('manage-users')) {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $meetingTitle = $meeting->title;
        $meeting->delete();

        ActivityLog::log('meeting_deleted', "Réunion supprimée: {$meetingTitle}");

        return redirect()->route('web.meetings')->with('success', 'Réunion supprimée avec succès.');
    }

    /**
     * Join a meeting.
     */
    public function join(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        // Check access
        if (!$user->isSuperAdmin() && $user->organization_id !== $meeting->organization_id) {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Check if meeting is ongoing
        if ($meeting->status !== 'ongoing') {
            return redirect()->back()->with('error', 'La réunion n\'est pas en cours.');
        }

        // Update participant join time
        $meeting->participants()->updateExistingPivot($user->id, [
            'joined_at' => now(),
        ]);

        ActivityLog::log('meeting_joined', "Réunion rejointe: {$meeting->title}");

        // Redirect to meeting link
        if ($meeting->meeting_link) {
            return redirect()->away($meeting->meeting_link);
        }

        return redirect()->route('web.meetings.show', $meeting->id)->with('success', 'Vous avez rejoint la réunion.');
    }

    /**
     * Accept a meeting invitation.
     */
    public function accept(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        $participant = MeetingParticipant::where('meeting_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Vous n\'êtes pas invité à cette réunion.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        $participant->update(['status' => 'accepted']);

        ActivityLog::log('meeting_accepted', "Invitation acceptée: {$meeting->title}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Invitation acceptée.'], 200);
        }

        return redirect()->back()->with('success', 'Invitation acceptée.');
    }

    /**
     * Decline a meeting invitation.
     */
    public function decline(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        $participant = MeetingParticipant::where('meeting_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Vous n\'êtes pas invité à cette réunion.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        $participant->update(['status' => 'declined']);

        ActivityLog::log('meeting_declined', "Invitation déclinée: {$meeting->title}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Invitation déclinée.'], 200);
        }

        return redirect()->back()->with('success', 'Invitation déclinée.');
    }

    /**
     * Start a meeting.
     */
    public function start(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        // Only creator can start
        if ($user->id !== $meeting->created_by) {
            return redirect()->back()->with('error', 'Seul le créateur peut démarrer la réunion.');
        }

        $meeting->start();

        ActivityLog::log('meeting_started', "Réunion démarrée: {$meeting->title}");

        return redirect()->route('web.meetings.show', $meeting->id)->with('success', 'Réunion démarrée avec succès.');
    }

    /**
     * End a meeting.
     */
    public function end(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        // Only creator can end
        if ($user->id !== $meeting->created_by) {
            return redirect()->back()->with('error', 'Seul le créateur peut terminer la réunion.');
        }

        $meeting->end();

        ActivityLog::log('meeting_ended', "Réunion terminée: {$meeting->title}");

        return redirect()->route('web.meetings.show', $meeting->id)->with('success', 'Réunion terminée avec succès.');
    }

    /**
     * Save AI summary for meeting.
     */
    public function saveSummary(Request $request, $id)
    {
        $user = $request->user();
        $meeting = Meeting::findOrFail($id);

        // Only organizer or admin
        if ($user->id !== $meeting->organizer_id && !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'ai_summary' => 'required|string',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v);
        }

        $meeting->update(['ai_summary' => $request->ai_summary]);

        ActivityLog::log('meeting_summary_saved', "Résumé IA enregistré: {$meeting->title}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Résumé enregistré avec succès.'], 200);
        }

        return redirect()->back()->with('success', 'Résumé enregistré.');
    }
}

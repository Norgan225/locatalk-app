<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Project::with(['organization', 'department', 'creator', 'users', 'tasks']);

        // Super admin et owner (created_by) voient tous les projets
        // Les autres utilisateurs voient uniquement les projets auxquels ils participent
        if ($user && !$user->isSuperAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id)
                  ->where(function ($subQ) use ($user) {
                      // Voir les projets créés par l'utilisateur OU les projets où il est membre
                      $subQ->where('created_by', $user->id)
                           ->orWhereHas('users', function ($memberQuery) use ($user) {
                               $memberQuery->where('users.id', $user->id);
                           });
                  });
            });
        }

        // Optional filters
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by user participation
        if ($request->has('user_id')) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }

        // Clone query for statistics (before status filtering)
        $statsQuery = clone $query;
        $allProjectsForStats = $statsQuery->get();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        // Calculate stats
        $stats = [
            'total_projects' => $allProjectsForStats->count(),
            'active_projects' => $allProjectsForStats->where('status', 'active')->count(),
            'completed_projects' => $allProjectsForStats->where('status', 'completed')->count(),
            'total_tasks' => $allProjectsForStats->sum(function($p) { return $p->tasks->count(); }),
            'completed_tasks' => $allProjectsForStats->sum(function($p) { return $p->tasks->where('status', 'completed')->count(); }),
            'avg_completion' => $allProjectsForStats->count() > 0 ? round($allProjectsForStats->avg('progress')) : 0,
        ];

        // Add statistics to each project
        $projects->each(function ($project) use ($user) {
            $project->members_count = $project->users->count();
            $project->total_tasks = $project->tasks->count();
            $project->completed_tasks = $project->tasks->where('status', 'completed')->count();
            $project->completion_percentage = $project->progress ?? 0;

            // Ajouter le rôle de l'utilisateur dans le projet
            $projectUser = $project->users->firstWhere('id', $user->id);
            $project->user_role = $projectUser ? $projectUser->pivot->role : null;
            $project->is_owner = $project->created_by === $user->id;
        });

        if ($request->wantsJson()) {
            return response()->json(['data' => $projects], 200);
        }

        return view('projects.index', ['projects' => $projects, 'stats' => $stats]);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Non authentifié.'], 401)
                : redirect()->route('login');
        }

        $v = Validator::make($request->all(), [
            'organization_id' => 'required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,on_hold,completed,cancelled',
            'deadline' => 'nullable|date|after:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();

        // If not super admin, force organization_id to user's organization
        if (!$user->isSuperAdmin()) {
            $data['organization_id'] = $user->organization_id;
        }

        $data['created_by'] = $user->id;
        $data['status'] = $data['status'] ?? 'active';
        $data['progress'] = 0;

        $project = Project::create($data);

        // Attach users to project
        $userIds = $request->input('user_ids', []);
        if (!empty($userIds)) {
            $syncData = [];
            foreach ($userIds as $userId) {
                $syncData[$userId] = ['role' => 'member'];
            }
            $project->users()->sync($syncData);
        }

        // Add creator as project manager
        $project->users()->syncWithoutDetaching([
            $user->id => ['role' => 'manager']
        ]);

        // Log activity
        ActivityLog::log('project_created', "Projet créé: {$project->name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Projet créé avec succès.',
                'data' => $project->load(['organization', 'department', 'users'])
            ], 201);
        }

        return redirect()->route('projects.show', $project->id)->with('success', 'Projet créé avec succès.');
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $project = Project::with([
            'organization',
            'department',
            'creator',
            'users',
            'tasks.assignee',
            'comments' => function($q) {
                $q->whereNull('parent_id')->with('user', 'replies.user');
            }
        ])->findOrFail($id);

        // Check access (super admin can see everything, others only their org)
        if (!$user->isSuperAdmin() && $user->organization_id !== $project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Add detailed statistics
        $project->statistics = [
            'total_members' => $project->users()->count(),
            'total_tasks' => $project->tasks()->count(),
            'completed_tasks' => $project->tasks()->where('status', 'completed')->count(),
            'pending_tasks' => $project->tasks()->whereIn('status', ['todo', 'in_progress'])->count(),
            'overdue_tasks' => $project->tasks()
                ->where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
            'completion_percentage' => $project->progress ?? 0,
            'is_overdue' => $project->isOverdue(),
            'days_remaining' => $project->deadline ? now()->diffInDays($project->deadline, false) : null,
        ];

        if ($request->wantsJson()) {
            return response()->json(['data' => $project], 200);
        }

        return view('projects.show', ['project' => $project]);
    }

    /**
     * Store a comment for the project.
     */
    public function storeComment(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:project_comments,id'
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        $comment = $project->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        // Load user for the response
        $comment->load('user');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Commentaire ajouté',
                'comment' => $comment,
            ]);
        }

        return redirect()->route('web.projects.show', $id)->with('success', 'Commentaire ajouté');
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $project = Project::findOrFail($id);

        // Check access (super admin can update everything, others only their org)
        if (!$user->isSuperAdmin() && $user->organization_id !== $project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,on_hold,completed,cancelled',
            'deadline' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $project->update($v->validated());

        // Update team members if provided
        if ($request->has('user_ids')) {
            $userIds = $request->input('user_ids', []);
            $syncData = [];

            // Keep creator as manager
            foreach ($userIds as $userId) {
                if ($userId == $project->created_by) {
                    $syncData[$userId] = ['role' => 'manager'];
                } else {
                    $syncData[$userId] = ['role' => 'member'];
                }
            }

            // Ensure creator is always included
            if (!in_array($project->created_by, $userIds)) {
                $syncData[$project->created_by] = ['role' => 'manager'];
            }

            $project->users()->sync($syncData);
        }

        // Log activity
        ActivityLog::log('project_updated', "Projet mis à jour: {$project->name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Projet mis à jour avec succès.',
                'data' => $project->load(['organization', 'department', 'users'])
            ], 200);
        }

        return redirect()->route('web.projects.show', $project->id)->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $project = Project::findOrFail($id);

        // Check access (super admin or project creator)
        if (!$user->isSuperAdmin() && $user->id !== $project->created_by) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé. Seul le créateur peut supprimer ce projet.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $projectName = $project->name;
        $project->delete();

        // Log activity
        ActivityLog::log('project_deleted', "Projet supprimé: {$projectName}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Projet supprimé avec succès.'], 200);
        }

        if ($request->has('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Projet supprimé avec succès.');
        }

        return redirect()->route('web.projects')->with('success', 'Projet supprimé avec succès.');
    }

    /**
     * Assign users to project.
     */
    public function assignUsers(Request $request, $id)
    {
        $user = $request->user();
        $project = Project::findOrFail($id);

        // Check access (super admin can assign to any project, others only their org)
        if (!$user->isSuperAdmin() && $user->organization_id !== $project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'nullable|string|in:manager,member',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v);
        }

        $userIds = $request->input('user_ids');
        $role = $request->input('role', 'member');

        $syncData = [];
        foreach ($userIds as $userId) {
            $syncData[$userId] = ['role' => $role];
        }

        $project->users()->syncWithoutDetaching($syncData);

        // Log activity
        ActivityLog::log('project_users_assigned', "Membres assignés au projet: {$project->name}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Utilisateurs assignés avec succès.',
                'data' => $project->load('users')
            ], 200);
        }

        return redirect()->back()->with('success', 'Utilisateurs assignés avec succès.');
    }

    /**
     * Remove user from project.
     */
    public function removeUser(Request $request, $id, $userId)
    {
        $user = $request->user();
        $project = Project::findOrFail($id);

        // Check access (super admin can remove from any project, others only their org)
        if (!$user->isSuperAdmin() && $user->organization_id !== $project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $project->users()->detach($userId);

        // Log activity
        ActivityLog::log('project_user_removed', "Membre retiré du projet: {$project->name}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Utilisateur retiré du projet avec succès.'], 200);
        }

        return redirect()->back()->with('success', 'Utilisateur retiré du projet.');
    }

    /**
     * Update project progress (recalculate based on tasks).
     */
    public function updateProgress(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->updateProgress();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Progression mise à jour.',
                'data' => ['progress' => $project->progress]
            ], 200);
        }

        return redirect()->back()->with('success', 'Progression mise à jour.');
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Task::with(['project', 'assignee', 'creator']);

        // Filter by organization via project (super admin sees all)
        if ($user && !$user->isSuperAdmin()) {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        // Optional filters
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter overdue tasks
        if ($request->boolean('overdue')) {
            $query->where('status', '!=', 'completed')
                ->where('due_date', '<', now());
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        // Add status info
        $tasks->each(function ($task) {
            $task->is_overdue = $task->isOverdue();
        });

        if ($request->wantsJson()) {
            return response()->json(['data' => $tasks], 200);
        }

        return view('tasks.index', ['tasks' => $tasks]);
    }

    /**
     * Store a newly created task.
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
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:todo,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();

        // Verify project access (super admin can create tasks in any project)
        $project = Project::findOrFail($data['project_id']);
        if (!$user->isSuperAdmin() && $user->organization_id !== $project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé au projet.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $data['created_by'] = $user->id;
        $data['priority'] = $data['priority'] ?? 'medium';
        $data['status'] = $data['status'] ?? 'todo';

        $task = Task::create($data);

        // Update project progress
        $project->updateProgress();

        // Log activity
        ActivityLog::log('task_created', "Tâche créée: {$task->title}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Tâche créée avec succès.',
                'data' => $task->load(['project', 'assignee', 'creator'])
            ], 201);
        }

        return redirect()->route('web.tasks')->with('success', 'Tâche créée avec succès.');
    }

    /**
     * Display the specified task.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $task = Task::with([
            'project',
            'assignee',
            'creator',
            'comments' => function($q) {
                $q->whereNull('parent_id')->with('user', 'replies.user');
            }
        ])->findOrFail($id);

        // Check access (super admin sees everything, others only their org)
        if (!$user->isSuperAdmin() && $user->organization_id !== $task->project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Add extra info
        $task->is_overdue = $task->isOverdue();
        $task->days_remaining = $task->due_date ? now()->diffInDays($task->due_date, false) : null;

        if ($request->wantsJson()) {
            return response()->json(['data' => $task], 200);
        }

        return view('tasks.show', ['task' => $task]);
    }

    /**
     * Store a comment for the task.
     */
    public function storeComment(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:task_comments,id'
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        $comment = $task->comments()->create([
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

        return redirect()->route('web.tasks.show', $id)->with('success', 'Commentaire ajouté');
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::findOrFail($id);

        // Check access (super admin can update any task, others only their org)
        if (!$user->isSuperAdmin() && $user->organization_id !== $task->project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:todo,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v)->withInput();
        }

        $oldStatus = $task->status;
        $task->update($v->validated());

        // If status changed to completed, update project progress
        if ($oldStatus !== $task->status && $task->status === 'completed') {
            $task->project->updateProgress();
        }

        // Log activity
        ActivityLog::log('task_updated', "Tâche mise à jour: {$task->title}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Tâche mise à jour avec succès.',
                'data' => $task->load(['project', 'assignee'])
            ], 200);
        }

        return redirect()->route('web.tasks.show', $task->id)->with('success', 'Tâche mise à jour avec succès.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::findOrFail($id);

        // Check access (super admin, project creator, or task creator)
        $project = $task->project;
        if (!$user->isSuperAdmin() &&
            $user->id !== $task->created_by &&
            $user->id !== $project->created_by) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $taskTitle = $task->title;
        $projectId = $task->project_id;

        $task->delete();

        // Update project progress
        Project::find($projectId)?->updateProgress();

        // Log activity
        ActivityLog::log('task_deleted', "Tâche supprimée: {$taskTitle}");

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Tâche supprimée avec succès.'], 200);
        }

        return redirect()->route('web.tasks')->with('success', 'Tâche supprimée avec succès.');
    }

    /**
     * Mark task as completed.
     */
    public function complete(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::findOrFail($id);

        // Check access (super admin, assignee or project member from same org)
        if (!$user->isSuperAdmin() &&
            $user->id !== $task->assigned_to &&
            $user->organization_id !== $task->project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $task->markAsCompleted();

        // Log activity
        ActivityLog::log('task_completed', "Tâche complétée: {$task->title}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Tâche marquée comme complétée.',
                'data' => $task
            ], 200);
        }

        return redirect()->back()->with('success', 'Tâche marquée comme complétée.');
    }

    /**
     * Change task status.
     */
    public function changeStatus(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::findOrFail($id);

        // Check access (super admin, assignee or project member from same org)
        if (!$user->isSuperAdmin() &&
            $user->id !== $task->assigned_to &&
            $user->organization_id !== $task->project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'status' => 'required|in:todo,in_progress,completed,cancelled',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v);
        }

        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);

        // Update project progress if status changed
        if ($oldStatus !== $task->status) {
            $task->project->updateProgress();
        }

        // Log activity
        ActivityLog::log('task_status_changed', "Statut tâche modifié: {$task->title} ({$oldStatus} -> {$task->status})");

        if (method_exists($task, 'activities')) {
            $statusLabels = [
                'todo' => 'À faire',
                'in_progress' => 'En cours',
                'completed' => 'Terminée',
                'cancelled' => 'Annulée',
            ];
            $oldLabel = $statusLabels[$oldStatus] ?? $oldStatus;
            $newLabel = $statusLabels[$task->status] ?? $task->status;

            $task->activities()->create([
                'user_id' => auth()->id(),
                'action' => 'status_changed',
                'description' => "a changé le statut de \"$oldLabel\" à \"$newLabel\"",
            ]);
        }

        if ($request->wantsJson()) {
            // Calculate project statistics for frontend update
            $project = $task->project;
            $statistics = [
                'total_tasks' => $project->tasks()->count(),
                'completed_tasks' => $project->tasks()->where('status', 'completed')->count(),
                'pending_tasks' => $project->tasks()->whereIn('status', ['todo', 'in_progress'])->count(),
                'completion_percentage' => $project->progress ?? 0,
            ];

            return response()->json([
                'message' => 'Statut de la tâche mis à jour.',
                'data' => $task,
                'project_statistics' => $statistics
            ], 200);
        }

        return redirect()->back()->with('success', 'Statut de la tâche mis à jour.');
    }

    /**
     * Assign task to user.
     */
    public function assign(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::findOrFail($id);

        // Check access (super admin can assign any task, others only their org)
        if (!$user->isSuperAdmin() && $user->organization_id !== $task->project->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }
            return redirect()->back()->withErrors($v);
        }

        $task->update(['assigned_to' => $request->assigned_to]);

        // Log activity
        $assignee = \App\Models\User::find($request->assigned_to);
        ActivityLog::log('task_assigned', "Tâche assignée à {$assignee->name}: {$task->title}");

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Tâche assignée avec succès.',
                'data' => $task->load('assignee')
            ], 200);
        }

        return redirect()->back()->with('success', 'Tâche assignée avec succès.');
    }

    /**
     * Get my tasks (current user).
     */
    public function myTasks(Request $request)
    {
        $user = $request->user();

        $query = Task::with(['project', 'creator'])
            ->where('assigned_to', $user->id);

        // Optional status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        $tasks->each(function ($task) {
            $task->is_overdue = $task->isOverdue();
        });

        if ($request->wantsJson()) {
            return response()->json(['data' => $tasks], 200);
        }

        return view('tasks.my-tasks', ['tasks' => $tasks]);
    }

    /**
     * Update task status (alias for changeStatus for drag & drop).
     */
    public function updateStatus(Request $request, $id)
    {
        return $this->changeStatus($request, $id);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\Message;
use App\Models\Channel;
use App\Models\Organization;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics and metrics.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin a son propre dashboard
        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard($request, $user);
        }

        // Dashboard standard pour les autres utilisateurs
        return $this->standardDashboard($request, $user);
    }

    /**
     * Dashboard for Super Admin - Platform overview.
     */
    private function superAdminDashboard(Request $request, User $user)
    {
        $stats = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'platform' => $this->getPlatformStats(),
            'organizations' => $this->getOrganizationsOverview(),
            'recent_activity' => $this->getRecentActivity(null, 15),
        ];

        if ($request->wantsJson()) {
            return response()->json(['data' => $stats], 200);
        }

        return view('dashboard-super-admin', ['stats' => $stats]);
    }

    /**
     * Dashboard for regular users (owner, admin, responsable, employe).
     */
    private function standardDashboard(Request $request, User $user)
    {
        $organizationId = $user->organization_id;

        // Base statistics accessible to all users
        $stats = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $user->department ? $user->department->name : 'N/A',
            ],
            'personal' => [
                'my_tasks' => $this->getMyTasks($user),
                'my_projects' => $this->getMyProjects($user),
                'unread_messages' => $this->getUnreadMessages($user),
                'my_channels' => $this->getMyChannels($user),
            ],
        ];

        // Additional stats for owner/admin
        if ($user->canManageUsers() && $organizationId) {
            $orgStats = $this->getOrganizationStats($organizationId, $user);
            $deptStats = $this->getDepartmentsStats($organizationId);
            $projectStats = $this->getProjectsStats($organizationId);
            $userStats = $this->getUsersStats($organizationId);

            // Get project IDs for this organization
            $projectIds = Project::where('organization_id', $organizationId)->pluck('id');

            // Count tasks for this organization
            $orgTasks = Task::whereIn('project_id', $projectIds);
            $tasksStats = [
                'total' => $orgTasks->count(),
                'todo' => (clone $orgTasks)->where('status', 'todo')->count(),
                'in_progress' => (clone $orgTasks)->where('status', 'in_progress')->count(),
                'completed' => (clone $orgTasks)->where('status', 'completed')->count(),
                'overdue' => (clone $orgTasks)->where('status', '!=', 'completed')->where('due_date', '<', now())->count(),
            ];

            // Format organization stats for the view
            $stats['organization'] = array_merge($orgStats, [
                'total_users' => $userStats['total'],
                'active_users' => $userStats['active'],
                'online_users' => $userStats['online'],
                'total_departments' => $deptStats['total'],
                'active_departments' => $deptStats['active'],
                'total_projects' => $projectStats['total'],
                'active_projects' => $projectStats['active'],
                'completed_projects' => $projectStats['completed'],
                'projects_stats' => $projectStats,
                'total_tasks' => $tasksStats['total'],
                'tasks_stats' => $tasksStats,
            ]);

            $stats['recent_activity'] = $this->getRecentActivity($organizationId, 10);
        }

        if ($request->wantsJson()) {
            return response()->json(['data' => $stats], 200);
        }

        return view('dashboard', ['stats' => $stats]);
    }

    /**
     * Get overview of all organizations (super admin only).
     */
    private function getOrganizationsOverview(): array
    {
        $organizations = Organization::withCount(['users', 'departments'])
            ->with(['users' => function($query) {
                $query->select('organization_id', 'role', 'status')
                    ->groupBy('organization_id', 'role', 'status');
            }])
            ->get();

        return $organizations->map(function ($org) {
            $projectIds = Project::where('organization_id', $org->id)->pluck('id');
            $tasksCount = Task::whereIn('project_id', $projectIds)->count();

            return [
                'id' => $org->id,
                'name' => $org->name,
                'plan' => $org->plan ?? 'N/A',
                'subscription_status' => $org->subscription_status ?? 'N/A',
                'users_count' => $org->users_count,
                'departments_count' => $org->departments_count,
                'projects_count' => Project::where('organization_id', $org->id)->count(),
                'tasks_count' => $tasksCount,
                'active_users' => User::where('organization_id', $org->id)->where('status', 'active')->count(),
                'is_active' => $org->isSubscriptionActive(),
            ];
        })->toArray();
    }

    /**
     * Get current user's tasks statistics.
     */
    private function getMyTasks(User $user): array
    {
        $myTasks = Task::where('assigned_to', $user->id);

        return [
            'total' => $myTasks->count(),
            'todo' => (clone $myTasks)->where('status', 'todo')->count(),
            'in_progress' => (clone $myTasks)->where('status', 'in_progress')->count(),
            'completed' => (clone $myTasks)->where('status', 'completed')->count(),
            'overdue' => (clone $myTasks)->where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
            'high_priority' => (clone $myTasks)->where('priority', 'high')
                ->where('status', '!=', 'completed')
                ->count(),
        ];
    }

    /**
     * Get current user's projects.
     */
    private function getMyProjects(User $user): array
    {
        $myProjects = $user->projects();

        return [
            'total' => $myProjects->count(),
            'active' => (clone $myProjects)->where('status', 'active')->count(),
            'completed' => (clone $myProjects)->where('status', 'completed')->count(),
            'on_hold' => (clone $myProjects)->where('status', 'on_hold')->count(),
        ];
    }

    /**
     * Get unread messages count.
     */
    private function getUnreadMessages(User $user): int
    {
        return Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get user's channels count.
     */
    private function getMyChannels(User $user): array
    {
        return [
            'total' => $user->channels()->count(),
        ];
    }

    /**
     * Get organization-level statistics.
     */
    private function getOrganizationStats(int $organizationId, User $user): array
    {
        $organization = Organization::find($organizationId);

        if (!$organization) {
            return [];
        }

        return [
            'name' => $organization->name,
            'plan' => $organization->plan ?? 'N/A',
            'subscription_status' => $organization->subscription_status ?? 'N/A',
            'subscription_expires_at' => $organization->subscription_expires_at
                ? $organization->subscription_expires_at->format('d/m/Y')
                : 'N/A',
            'max_users' => $organization->max_users ?? 0,
            'current_users' => User::where('organization_id', $organizationId)->count(),
            'remaining_slots' => $organization->remainingUserSlots(),
            'is_active' => $organization->isSubscriptionActive(),
            'allow_remote_access' => $organization->allow_remote_access ?? false,
        ];
    }

    /**
     * Get departments statistics.
     */
    private function getDepartmentsStats(int $organizationId): array
    {
        $departments = Department::where('organization_id', $organizationId);

        return [
            'total' => $departments->count(),
            'active' => (clone $departments)->where('is_active', true)->count(),
            'inactive' => (clone $departments)->where('is_active', false)->count(),
        ];
    }

    /**
     * Get projects statistics.
     */
    private function getProjectsStats(int $organizationId): array
    {
        $projects = Project::where('organization_id', $organizationId);

        return [
            'total' => $projects->count(),
            'active' => (clone $projects)->where('status', 'active')->count(),
            'completed' => (clone $projects)->where('status', 'completed')->count(),
            'on_hold' => (clone $projects)->where('status', 'on_hold')->count(),
            'overdue' => (clone $projects)->where('status', '!=', 'completed')
                ->where('deadline', '<', now())
                ->count(),
        ];
    }

    /**
     * Get users statistics.
     */
    private function getUsersStats(int $organizationId): array
    {
        $users = User::where('organization_id', $organizationId);

        return [
            'total' => $users->count(),
            'active' => (clone $users)->where('status', 'active')->count(),
            'inactive' => (clone $users)->where('status', 'inactive')->count(),
            'suspended' => (clone $users)->where('status', 'suspended')->count(),
            'online' => (clone $users)->where('last_login_at', '>', now()->subMinutes(15))->count(),
            'by_role' => [
                'owner' => (clone $users)->where('role', 'owner')->count(),
                'admin' => (clone $users)->where('role', 'admin')->count(),
                'responsable' => (clone $users)->where('role', 'responsable')->count(),
                'employee' => (clone $users)->where('role', 'employee')->count(),
            ],
        ];
    }

    /**
     * Get recent activity logs.
     */
    private function getRecentActivity(?int $organizationId, int $limit = 10): array
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        // Filter by organization if specified (not super_admin)
        if ($organizationId !== null) {
            $query->where('organization_id', $organizationId);
        }

        $activities = $query->get();

        return $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'user' => $activity->user ? $activity->user->name : 'Système',
                'action' => $activity->action,
                'description' => $activity->description,
                'ip_address' => $activity->ip_address,
                'created_at' => $activity->created_at->format('d/m/Y H:i:s'),
                'time_ago' => $activity->created_at->diffForHumans(),
            ];
        })->toArray();
    }

    /**
     * Get platform-level statistics (owner only).
     */
    private function getPlatformStats(): array
    {
        return [
            'total_organizations' => Organization::count(),
            'active_organizations' => Organization::where('subscription_status', 'active')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'total_messages' => Message::count(),
            'total_channels' => Channel::count(),
            'total_departments' => Department::count(),
        ];
    }

    /**
     * Get analytics data (for charts and graphs).
     */
    public function analytics(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $organizationId = $user->isSuperAdmin() ? null : $user->organization_id;
        $period = $request->input('period', '30'); // days

        $analytics = [
            'users_growth' => $this->getUsersGrowth($organizationId, $period),
            'projects_completion' => $this->getProjectsCompletion($organizationId, $period),
            'tasks_completion' => $this->getTasksCompletion($organizationId, $period),
            'messages_activity' => $this->getMessagesActivity($organizationId, $period),
        ];

        if ($request->wantsJson()) {
            return response()->json(['data' => $analytics], 200);
        }

        return view('dashboard.analytics', ['analytics' => $analytics]);
    }

    /**
     * Get users growth over time.
     */
    private function getUsersGrowth(?int $organizationId, int $days): array
    {
        $query = User::where('created_at', '>', now()->subDays($days));

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $growth = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $growth->map(function ($item) {
            return [
                'date' => $item->date,
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Get projects completion rate.
     */
    private function getProjectsCompletion(?int $organizationId, int $days): array
    {
        $query = Project::where('updated_at', '>', now()->subDays($days));

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $projects = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return $projects->map(function ($item) {
            return [
                'status' => $item->status,
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Get tasks completion rate.
     */
    private function getTasksCompletion(?int $organizationId, int $days): array
    {
        $query = Task::where('updated_at', '>', now()->subDays($days));

        if ($organizationId) {
            $query->whereHas('project', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }

        $tasks = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return $tasks->map(function ($item) {
            return [
                'status' => $item->status,
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Get messages activity over time.
     */
    private function getMessagesActivity(?int $organizationId, int $days): array
    {
        $query = Message::where('created_at', '>', now()->subDays($days));

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $messages = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $messages->map(function ($item) {
            return [
                'date' => $item->date,
                'count' => $item->count,
            ];
        })->toArray();
    }
}

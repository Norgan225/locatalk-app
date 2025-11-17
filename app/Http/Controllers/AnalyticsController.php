<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\Channel;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', Carbon::now()->subDays(30));
        $endDate = $request->input('end_date', Carbon::now());

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Base query for organization filtering
        $orgId = $user->organization_id;

        // Projects Analytics
        $projectsStats = [
            'total' => Project::where('organization_id', $orgId)->count(),
            'active' => Project::where('organization_id', $orgId)->where('status', 'active')->count(),
            'completed' => Project::where('organization_id', $orgId)->where('status', 'completed')->count(),
            'on_hold' => Project::where('organization_id', $orgId)->where('status', 'on_hold')->count(),
            'by_month' => Project::where('organization_id', $orgId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        // Tasks Analytics
        $tasksStats = [
            'total' => Task::whereHas('project', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->count(),
            'todo' => Task::whereHas('project', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->where('status', 'todo')->count(),
            'in_progress' => Task::whereHas('project', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->where('status', 'in_progress')->count(),
            'completed' => Task::whereHas('project', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->where('status', 'completed')->count(),
            'by_priority' => [
                'low' => Task::whereHas('project', function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId);
                })->where('priority', 'low')->count(),
                'medium' => Task::whereHas('project', function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId);
                })->where('priority', 'medium')->count(),
                'high' => Task::whereHas('project', function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId);
                })->where('priority', 'high')->count(),
                'urgent' => Task::whereHas('project', function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId);
                })->where('priority', 'urgent')->count(),
            ],
        ];

        // Users Analytics
        $usersStats = [
            'total' => User::where('organization_id', $orgId)->count(),
            'active' => User::where('organization_id', $orgId)->where('status', 'active')->count(),
            'inactive' => User::where('organization_id', $orgId)->whereIn('status', ['suspended', 'inactive'])->count(),
            'by_department' => Department::where('organization_id', $orgId)
                ->withCount('users')
                ->get()
                ->map(function ($dept) {
                    return [
                        'name' => $dept->name,
                        'count' => $dept->users_count,
                    ];
                }),
            'by_role' => [
                'owner' => User::where('organization_id', $orgId)->where('role', 'owner')->count(),
                'responsable' => User::where('organization_id', $orgId)->where('role', 'responsable')->count(),
                'employe' => User::where('organization_id', $orgId)->where('role', 'employe')->count(),
            ],
        ];

        // Meetings Analytics
        $meetingsStats = [
            'total' => Meeting::where('organization_id', $orgId)->count(),
            'scheduled' => Meeting::where('organization_id', $orgId)->where('status', 'scheduled')->count(),
            'ongoing' => Meeting::where('organization_id', $orgId)->where('status', 'ongoing')->count(),
            'completed' => Meeting::where('organization_id', $orgId)->where('status', 'completed')->count(),
            'cancelled' => Meeting::where('organization_id', $orgId)->where('status', 'cancelled')->count(),
            'upcoming' => Meeting::where('organization_id', $orgId)
                ->where('status', 'scheduled')
                ->where('start_time', '>', Carbon::now())
                ->count(),
            'by_month' => Meeting::where('organization_id', $orgId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->selectRaw('DATE_FORMAT(start_time, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        // Messages Analytics
        $messagesStats = [
            'total' => Message::whereHas('channel', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->count(),
            'today' => Message::whereHas('channel', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->whereDate('created_at', Carbon::today())->count(),
            'this_week' => Message::whereHas('channel', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'this_month' => Message::whereHas('channel', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })->whereMonth('created_at', Carbon::now()->month)->count(),
            'by_day' => Message::whereHas('channel', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
                ->groupBy('day')
                ->orderBy('day')
                ->get(),
        ];

        // Channels Analytics
        $channelsStats = [
            'total' => Channel::where('organization_id', $orgId)->count(),
            'public' => Channel::where('organization_id', $orgId)->where('type', 'public')->count(),
            'private' => Channel::where('organization_id', $orgId)->where('type', 'private')->count(),
            'department' => Channel::where('organization_id', $orgId)->where('type', 'department')->count(),
            'most_active' => Channel::where('organization_id', $orgId)
                ->withCount('messages')
                ->orderBy('messages_count', 'desc')
                ->limit(5)
                ->get(),
        ];

        // Departments Analytics
        $departmentsStats = [
            'total' => Department::where('organization_id', $orgId)->count(),
            'active' => Department::where('organization_id', $orgId)->where('is_active', true)->count(),
            'inactive' => Department::where('organization_id', $orgId)->where('is_active', false)->count(),
        ];

        // Activity Timeline (recent activities across all modules)
        $recentActivities = collect([
            Project::where('organization_id', $orgId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("'project' as type, name as title, created_at")
                ->get(),
            Task::whereHas('project', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("'task' as type, title, created_at")
                ->get(),
            Meeting::where('organization_id', $orgId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("'meeting' as type, title, created_at")
                ->get(),
        ])->flatten()->sortByDesc('created_at')->take(10);

        if ($request->wantsJson()) {
            return response()->json([
                'projects' => $projectsStats,
                'tasks' => $tasksStats,
                'users' => $usersStats,
                'meetings' => $meetingsStats,
                'messages' => $messagesStats,
                'channels' => $channelsStats,
                'departments' => $departmentsStats,
                'recent_activities' => $recentActivities,
            ], 200);
        }

        return view('analytics.index', compact(
            'projectsStats',
            'tasksStats',
            'usersStats',
            'meetingsStats',
            'messagesStats',
            'channelsStats',
            'departmentsStats',
            'recentActivities',
            'startDate',
            'endDate'
        ));
    }
}

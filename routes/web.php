<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\OrganizationSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.view');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::patch('/profile/info', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Projects web routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('web.projects');
    Route::get('/projects/create', function () {
        return view('projects.create');
    })->name('web.projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('web.projects.store');
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('web.projects.show');
    Route::get('/projects/{id}/edit', function ($id) {
        $project = \App\Models\Project::with(['users', 'organization', 'department'])->findOrFail($id);
        return view('projects.edit', ['project' => $project]);
    })->name('web.projects.edit');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('web.projects.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('web.projects.destroy');
    Route::post('/projects/{id}/comments', [ProjectController::class, 'storeComment'])->name('web.projects.comments.store');

    // Tasks web routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('web.tasks');
    Route::get('/tasks/create', function () {
        $projects = \App\Models\Project::where('organization_id', auth()->user()->organization_id)->get();
        $users = \App\Models\User::where('organization_id', auth()->user()->organization_id)->get();
        return view('tasks.create', compact('projects', 'users'));
    })->name('web.tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('web.tasks.store');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('web.tasks.show');
    Route::post('/tasks/{id}/comments', [TaskController::class, 'storeComment'])->name('web.tasks.comments.store');
    Route::get('/tasks/{id}/edit', function ($id) {
        $task = \App\Models\Task::with(['project'])->findOrFail($id);
        $projects = \App\Models\Project::where('organization_id', auth()->user()->organization_id)->get();
        $users = \App\Models\User::where('organization_id', auth()->user()->organization_id)->get();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    })->name('web.tasks.edit');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('web.tasks.update');
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('web.tasks.updateStatus');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('web.tasks.destroy');

    // Task Checklist routes
    Route::post('/tasks/{id}/checklist', [\App\Http\Controllers\TaskChecklistController::class, 'store'])->name('web.tasks.checklist.store');
    Route::patch('/tasks/checklist/{id}', [\App\Http\Controllers\TaskChecklistController::class, 'update'])->name('web.tasks.checklist.update');
    Route::delete('/tasks/checklist/{id}', [\App\Http\Controllers\TaskChecklistController::class, 'destroy'])->name('web.tasks.checklist.destroy');

    // Task Tags routes
    Route::post('/tasks/{id}/tags', [\App\Http\Controllers\TaskTagController::class, 'store'])->name('web.tasks.tags.store');
    Route::delete('/tasks/{id}/tags/{tagId}', [\App\Http\Controllers\TaskTagController::class, 'destroy'])->name('web.tasks.tags.destroy');

    // Task Attachments routes
    Route::post('/tasks/{id}/attachments', [\App\Http\Controllers\TaskAttachmentController::class, 'store'])->name('web.tasks.attachments.store');
    Route::delete('/tasks/{id}/attachments/{attachmentId}', [\App\Http\Controllers\TaskAttachmentController::class, 'destroy'])->name('web.tasks.attachments.destroy');

    // Messages web routes - Interface moderne par défaut
    Route::get('/messages', function () {
        return view('messages.modern');
    })->name('web.messages');

    // Interface moderne (alias)
    Route::get('/messages/modern', function () {
        return view('messages.modern');
    })->name('web.messages.modern');

    // Interface classique (legacy)
    Route::get('/messages/classic', function () {
        return view('messages.index');
    })->name('web.messages.classic');

    // Calls web routes
    Route::get('/calls', [App\Http\Controllers\CallController::class, 'index'])->name('web.calls');
    Route::post('/calls', [App\Http\Controllers\CallController::class, 'store'])->name('web.calls.store');
    Route::get('/calls/{id}', [App\Http\Controllers\CallController::class, 'show'])->name('web.calls.show');
    Route::post('/calls/{id}/end', [App\Http\Controllers\CallController::class, 'end'])->name('web.calls.end');

    // Call signaling routes (WebRTC)
    Route::post('/api/call-signal', [App\Http\Controllers\CallSignalController::class, 'sendSignal']);
    Route::post('/api/calls/{id}/accept', [App\Http\Controllers\CallSignalController::class, 'acceptCall']);
    Route::post('/api/calls/{id}/reject', [App\Http\Controllers\CallSignalController::class, 'rejectCall']);

    // Analytics web routes
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('web.analytics');

    // Organization Settings routes (Owner Only)
    Route::get('/settings', [OrganizationSettingsController::class, 'index'])->name('web.settings');
    Route::put('/settings/general', [OrganizationSettingsController::class, 'updateGeneral'])->name('web.settings.general');
    Route::put('/settings/logo', [OrganizationSettingsController::class, 'updateLogo'])->name('web.settings.logo');
    Route::put('/settings/branding', [OrganizationSettingsController::class, 'updateBranding'])->name('web.settings.branding');
    Route::put('/settings/advanced', [OrganizationSettingsController::class, 'updateAdvanced'])->name('web.settings.advanced');

    // Settings Demo Page
    Route::get('/settings/demo', function () {
        return view('settings.demo');
    })->name('web.settings.demo');

    // Departments web routes
    Route::get('/departments', [DepartmentController::class, 'index'])->name('web.departments');
    Route::get('/departments/create', function () {
        $organizations = auth()->user()->isSuperAdmin() ? \App\Models\Organization::all() : collect([auth()->user()->organization]);
        return view('departments.create', compact('organizations'));
    })->name('web.departments.create')->middleware('can:manage-users');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('web.departments.store')->middleware('can:manage-users');
    Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('web.departments.show');
    Route::get('/departments/{id}/edit', function ($id) {
        $department = \App\Models\Department::with('organization')->findOrFail($id);
        $organizations = auth()->user()->isSuperAdmin() ? \App\Models\Organization::all() : collect([auth()->user()->organization]);
        $users = \App\Models\User::where('organization_id', $department->organization_id)->get();
        return view('departments.edit', compact('department', 'organizations', 'users'));
    })->name('web.departments.edit')->middleware('can:manage-users');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('web.departments.update')->middleware('can:manage-users');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('web.departments.destroy')->middleware('can:manage-users');
    Route::post('/departments/{id}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('web.departments.toggleStatus')->middleware('can:manage-users');
    Route::post('/departments/{id}/members', [DepartmentController::class, 'addMember'])->name('web.departments.members.add')->middleware('can:manage-users');
    Route::delete('/departments/{id}/members/{userId}', [DepartmentController::class, 'removeMember'])->name('web.departments.members.remove')->middleware('can:manage-users');

    // Channels web routes
    Route::get('/channels', [ChannelController::class, 'index'])->name('web.channels');
    Route::get('/channels/create', function () {
        return view('channels.create');
    })->name('web.channels.create')->middleware('can:manage-users');
    Route::post('/channels', [ChannelController::class, 'store'])->name('web.channels.store')->middleware('can:manage-users');
    Route::get('/channels/{id}', [ChannelController::class, 'show'])->name('web.channels.show');
    Route::get('/channels/{id}/edit', function ($id) {
        $channel = \App\Models\Channel::findOrFail($id);
        return view('channels.edit', compact('channel'));
    })->name('web.channels.edit')->middleware('can:manage-users');
    Route::put('/channels/{id}', [ChannelController::class, 'update'])->name('web.channels.update')->middleware('can:manage-users');
    Route::delete('/channels/{id}', [ChannelController::class, 'destroy'])->name('web.channels.destroy')->middleware('can:manage-users');
    Route::post('/channels/{id}/join', [ChannelController::class, 'join'])->name('web.channels.join');
    Route::delete('/channels/{id}/leave', [ChannelController::class, 'leave'])->name('web.channels.leave');

    // Meetings web routes
    Route::get('/meetings', [MeetingController::class, 'index'])->name('web.meetings');
    Route::get('/meetings/create', [MeetingController::class, 'create'])->name('web.meetings.create')->middleware('can:manage-users');
    Route::post('/meetings', [MeetingController::class, 'store'])->name('web.meetings.store')->middleware('can:manage-users');
    Route::get('/meetings/{id}', [MeetingController::class, 'show'])->name('web.meetings.show');
    Route::get('/meetings/{id}/room', function ($id) {
        $meeting = \App\Models\Meeting::with(['participants', 'creator'])->findOrFail($id);
        return view('meetings.room', compact('meeting'));
    })->name('web.meetings.room');
    Route::get('/meetings/{id}/edit', [MeetingController::class, 'edit'])->name('web.meetings.edit')->middleware('can:manage-users');
    Route::put('/meetings/{id}', [MeetingController::class, 'update'])->name('web.meetings.update')->middleware('can:manage-users');
    Route::delete('/meetings/{id}', [MeetingController::class, 'destroy'])->name('web.meetings.destroy')->middleware('can:manage-users');
    Route::post('/meetings/{id}/join', [MeetingController::class, 'join'])->name('web.meetings.join');
    Route::post('/meetings/{id}/start', [MeetingController::class, 'start'])->name('web.meetings.start');
    Route::post('/meetings/{id}/end', [MeetingController::class, 'end'])->name('web.meetings.end');
    Route::post('/meetings/{meeting}/participants/add', [MeetingController::class, 'addParticipants'])->name('web.meetings.participants.add');
    Route::delete('/meetings/{meeting}/participants/{user}', [MeetingController::class, 'removeParticipant'])->name('web.meetings.participants.remove');
    Route::patch('/meetings/{meeting}/status', [MeetingController::class, 'updateStatus'])->name('web.meetings.update-status');

    // Users management (Admin/Owner only)
    Route::get('/users', [UserController::class, 'indexWeb'])->name('web.users');
    Route::get('/users/create', function () {
        $organizations = \App\Models\Organization::all();
        $departments = \App\Models\Department::where('organization_id', auth()->user()->organization_id)->get();
        return view('users.create', compact('organizations', 'departments'));
    })->name('web.users.create');
    Route::post('/users', [UserController::class, 'storeWeb'])->name('web.users.store');
    Route::get('/users/{id}', [UserController::class, 'showWeb'])->name('web.users.show');
    Route::get('/users/{id}/edit', function ($id) {
        $user = \App\Models\User::with(['organization', 'department'])->findOrFail($id);
        $organizations = \App\Models\Organization::all();
        $departments = \App\Models\Department::where('organization_id', $user->organization_id)->get();
        return view('users.edit', compact('user', 'organizations', 'departments'));
    })->name('web.users.edit');
    Route::put('/users/{id}', [UserController::class, 'updateWeb'])->name('web.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroyWeb'])->name('web.users.destroy');
    Route::post('/users/{id}/restore', [UserController::class, 'restoreWeb'])->name('web.users.restore');

    // Organizations management (Super Admin only)
    Route::get('/organizations', [OrganizationController::class, 'indexWeb'])->name('web.organizations');
    Route::get('/organizations/{id}', [OrganizationController::class, 'showWeb'])->name('web.organizations.show');

    // Subscriptions management (Super Admin only)
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('web.subscriptions');
    Route::patch('/subscriptions/{organizationId}/plan', [SubscriptionController::class, 'updatePlan'])->name('web.subscriptions.updatePlan');

    // Language switching
    Route::post('/language/change', [App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('language.change');
});

// Broadcasting authentication routes - support session auth (web)
Illuminate\Support\Facades\Broadcast::routes(['middleware' => ['web', 'auth']]);

require __DIR__.'/auth.php';

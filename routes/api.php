<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Routes utilisateurs (protégées)
Route::middleware(['auth:sanctum'])->group(function () {
    // Dashboard & Analytics
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
    Route::get('dashboard/analytics', [\App\Http\Controllers\DashboardController::class, 'analytics']);

    // Profile management
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show']);
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update']);
    Route::post('profile/change-password', [\App\Http\Controllers\ProfileController::class, 'changePassword']);
    Route::post('profile/settings', [\App\Http\Controllers\ProfileController::class, 'updateSettings']);
    Route::get('profile/settings', [\App\Http\Controllers\ProfileController::class, 'getSettings']);
    Route::post('profile/avatar', [\App\Http\Controllers\ProfileController::class, 'uploadAvatar']);
    Route::delete('profile/avatar', [\App\Http\Controllers\ProfileController::class, 'deleteAvatar']);
    Route::get('profile/devices', [\App\Http\Controllers\ProfileController::class, 'devices']);
    Route::post('profile/devices/{id}/revoke', [\App\Http\Controllers\ProfileController::class, 'revokeDevice']);
    Route::delete('profile', [\App\Http\Controllers\ProfileController::class, 'destroy']);

    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/restore', [UserController::class, 'restore']);

    // Organizations API (protected) - controller en charge des opérations CRUD
    Route::apiResource('organizations', \App\Http\Controllers\OrganizationController::class);

    // Departments API (protected) - owner/admin peuvent gérer
    Route::apiResource('departments', \App\Http\Controllers\DepartmentController::class);
    Route::post('departments/{id}/restore', [\App\Http\Controllers\DepartmentController::class, 'restore']);
    Route::post('departments/{id}/toggle-status', [\App\Http\Controllers\DepartmentController::class, 'toggleStatus']);

    // Projects API
    Route::apiResource('projects', \App\Http\Controllers\ProjectController::class);
    Route::post('projects/{id}/assign-users', [\App\Http\Controllers\ProjectController::class, 'assignUsers']);
    Route::delete('projects/{id}/users/{userId}', [\App\Http\Controllers\ProjectController::class, 'removeUser']);
    Route::post('projects/{id}/update-progress', [\App\Http\Controllers\ProjectController::class, 'updateProgress']);

    // Tasks API
    Route::get('tasks/my-tasks', [\App\Http\Controllers\TaskController::class, 'myTasks']);
    Route::apiResource('tasks', \App\Http\Controllers\TaskController::class);
    Route::post('tasks/{id}/complete', [\App\Http\Controllers\TaskController::class, 'complete']);
    Route::post('tasks/{id}/change-status', [\App\Http\Controllers\TaskController::class, 'changeStatus']);
    Route::post('tasks/{id}/assign', [\App\Http\Controllers\TaskController::class, 'assign']);

    // Messages API
    Route::get('messages/conversations', [\App\Http\Controllers\MessageController::class, 'conversations']);
    Route::get('messages/unread-count', [\App\Http\Controllers\MessageController::class, 'unreadCount']);
    Route::post('messages/mark-all-read', [\App\Http\Controllers\MessageController::class, 'markAllAsRead']);
    Route::get('messages/search', [\App\Http\Controllers\MessageController::class, 'search']);
    Route::apiResource('messages', \App\Http\Controllers\MessageController::class);
    Route::post('messages/{id}/mark-read', [\App\Http\Controllers\MessageController::class, 'markAsRead']);
    Route::post('messages/mark-read', [\App\Http\Controllers\MessageController::class, 'markAsRead']);

    // Channels API
    Route::apiResource('channels', \App\Http\Controllers\ChannelController::class);
    Route::get('channels/{id}/messages', [\App\Http\Controllers\ChannelMessageController::class, 'index']);
    Route::post('channels/{id}/messages', [\App\Http\Controllers\ChannelMessageController::class, 'store']);
    Route::post('channels/{id}/messages/{messageId}/pin', [\App\Http\Controllers\ChannelMessageController::class, 'togglePin']);
    Route::post('channels/{id}/messages/{messageId}/react', [\App\Http\Controllers\ChannelMessageController::class, 'addReaction']);
    Route::delete('channels/{id}/messages/{messageId}', [\App\Http\Controllers\ChannelMessageController::class, 'destroy']);
    Route::get('channels/{id}/encryption-key', [\App\Http\Controllers\ChannelMessageController::class, 'encryptionKey']);
    Route::post('channels/{id}/encryption-key', [\App\Http\Controllers\ChannelMessageController::class, 'updateEncryptionKey']);
    Route::post('channels/{id}/typing', [\App\Http\Controllers\ChannelMessageController::class, 'typing']);
    Route::get('channels/{id}/pinned-messages', [\App\Http\Controllers\ChannelMessageController::class, 'getPinnedMessages']);
    Route::post('channels/{id}/join', [\App\Http\Controllers\ChannelController::class, 'join']);
    Route::post('channels/{id}/leave', [\App\Http\Controllers\ChannelController::class, 'leave']);
    Route::post('channels/{id}/members', [\App\Http\Controllers\ChannelController::class, 'addMembers']);
    Route::delete('channels/{id}/members/{userId}', [\App\Http\Controllers\ChannelController::class, 'removeMember']);

    // Calls API
    Route::get('calls/history', [\App\Http\Controllers\CallController::class, 'history']);
    Route::apiResource('calls', \App\Http\Controllers\CallController::class);
    Route::post('calls/{id}/answer', [\App\Http\Controllers\CallController::class, 'answer']);
    Route::post('calls/{id}/end', [\App\Http\Controllers\CallController::class, 'end']);
    Route::post('calls/{id}/reject', [\App\Http\Controllers\CallController::class, 'reject']);

    // Call Encryption API (E2E pour appels de groupe)
    Route::post('calls/{callId}/encryption/init', [\App\Http\Controllers\CallEncryptionController::class, 'initializeSession']);
    Route::post('calls/{callId}/encryption/join', [\App\Http\Controllers\CallEncryptionController::class, 'joinSession']);
    Route::post('calls/{callId}/encryption/leave', [\App\Http\Controllers\CallEncryptionController::class, 'leaveSession']);
    Route::post('calls/{callId}/encryption/end', [\App\Http\Controllers\CallEncryptionController::class, 'endSession']);
    Route::post('calls/{callId}/encryption/rotate', [\App\Http\Controllers\CallEncryptionController::class, 'rotateKey']);
    Route::get('calls/{callId}/encryption/info', [\App\Http\Controllers\CallEncryptionController::class, 'getSessionInfo']);
    Route::post('calls/{callId}/encryption/nonce', [\App\Http\Controllers\CallEncryptionController::class, 'generateNonce']);

    // Meetings API
    Route::apiResource('meetings', \App\Http\Controllers\MeetingController::class);
    Route::post('meetings/{id}/accept', [\App\Http\Controllers\MeetingController::class, 'accept']);
    Route::post('meetings/{id}/decline', [\App\Http\Controllers\MeetingController::class, 'decline']);
    Route::post('meetings/{id}/start', [\App\Http\Controllers\MeetingController::class, 'start']);
    Route::post('meetings/{id}/end', [\App\Http\Controllers\MeetingController::class, 'end']);
    Route::post('meetings/{id}/summary', [\App\Http\Controllers\MeetingController::class, 'saveSummary']);

    // Notifications API
    Route::get('notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount']);
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/delete-all-read', [\App\Http\Controllers\NotificationController::class, 'deleteAllRead']);
    Route::apiResource('notifications', \App\Http\Controllers\NotificationController::class)->only(['index', 'destroy']);
    Route::post('notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::get('notifications/check', [\App\Http\Controllers\NotificationController::class, 'checkNew']);

    // User Status API (Présence en temps réel)
    Route::get('status', [\App\Http\Controllers\UserStatusController::class, 'show']);
    Route::put('status', [\App\Http\Controllers\UserStatusController::class, 'update']);
    Route::post('status/online', [\App\Http\Controllers\UserStatusController::class, 'setOnline']);
    Route::post('status/offline', [\App\Http\Controllers\UserStatusController::class, 'setOffline']);
    Route::post('status/away', [\App\Http\Controllers\UserStatusController::class, 'setAway']);
    Route::post('status/busy', [\App\Http\Controllers\UserStatusController::class, 'setBusy']);
    Route::post('status/invisible', [\App\Http\Controllers\UserStatusController::class, 'toggleInvisible']);
    Route::post('status/ping', [\App\Http\Controllers\UserStatusController::class, 'ping']);
    Route::get('status/user/{userId}', [\App\Http\Controllers\UserStatusController::class, 'getUserStatus']);
    Route::post('status/bulk', [\App\Http\Controllers\UserStatusController::class, 'bulkStatus']);
    Route::get('status/online', [\App\Http\Controllers\UserStatusController::class, 'getOnlineUsers']);

    // Messaging API (Interface moderne)
    Route::get('messaging/users', [\App\Http\Controllers\MessagingController::class, 'getAvailableUsers']);
    Route::get('messaging/conversations', [\App\Http\Controllers\MessagingController::class, 'getConversations']);
    Route::get('messaging/conversation/{userId}', [\App\Http\Controllers\MessagingController::class, 'getConversation']);
    Route::post('messaging/send', [\App\Http\Controllers\MessagingController::class, 'sendMessage']);
    Route::post('messaging/messages/{messageId}/react', [\App\Http\Controllers\MessagingController::class, 'addReaction']);
    Route::post('messaging/messages/{messageId}/pin', [\App\Http\Controllers\MessagingController::class, 'pinMessage']);
    Route::post('messaging/messages/{messageId}/unpin', [\App\Http\Controllers\MessagingController::class, 'unpinMessage']);
    Route::get('messaging/conversation/{userId}/pinned', [\App\Http\Controllers\MessagingController::class, 'getPinnedMessages']);
    Route::post('messaging/upload', [\App\Http\Controllers\MessagingController::class, 'uploadAttachment']);
    Route::post('messaging/messages/{messageId}/delivered', [\App\Http\Controllers\MessagingController::class, 'markAsDelivered']);
    Route::get('messaging/conversation/{userId}/search', [\App\Http\Controllers\MessagingController::class, 'searchInConversation']);
    Route::delete('messaging/messages/{messageId}', [\App\Http\Controllers\MessagingController::class, 'deleteMessage']);
    Route::post('messaging/typing', [\App\Http\Controllers\MessagingController::class, 'typing']);
    Route::post('messaging/link-preview', [\App\Http\Controllers\MessagingController::class, 'getLinkPreview']);

    Route::post('profile/update-e2e-key', [\App\Http\Controllers\ProfileController::class, 'updateE2EKey']);

    // E2E Encryption routes
    Route::get('users/{id}/public-key', [\App\Http\Controllers\UserController::class, 'getPublicKey']);
    Route::post('users/{id}/establish-e2e', [\App\Http\Controllers\UserController::class, 'establishE2EConnection']);
});

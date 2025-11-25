<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile (view mode).
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load(['organization', 'department']);

        // Get user stats
        $stats = [
            'projects' => $user->projects()->count(),
            'tasks' => $user->tasks()->count(),
            'messages' => $user->messages()->count(),
        ];

        // Get recent activity
        $recentActivity = $user->activityLogs()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('profile.show', compact('user', 'stats', 'recentActivity'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return Redirect::back()->with('success', 'Photo de profil mise à jour avec succès !');
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return Redirect::back()->with('success', 'Mot de passe mis à jour avec succès !');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Mettre à jour la clé publique E2E de l'utilisateur
     */
    public function updateE2EKey(Request $request)
    {
        $request->validate([
            'e2e_public_key' => 'required|string'
        ]);

        $user = $request->user();
        $user->update([
            'e2e_public_key' => $request->e2e_public_key
        ]);

        return response()->json([
            'message' => 'Clé E2E mise à jour avec succès'
        ]);
    }

    /**
     * Update user settings (API endpoint)
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'notifications_enabled' => 'boolean',
            'notification_sound' => 'string|in:bell,chime,notification,gentle',
            'notification_sound_enabled' => 'boolean',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'message' => 'Paramètres mis à jour avec succès',
            'settings' => $validated
        ]);
    }

    /**
     * Get user settings (API endpoint)
     */
    public function getSettings(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'notifications_enabled' => $user->notifications_enabled,
            'notification_sound' => $user->notification_sound ?: 'bell',
            'notification_sound_enabled' => $user->notification_sound_enabled !== false,
        ]);
    }
}

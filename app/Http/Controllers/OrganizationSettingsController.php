<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrganizationSettingsController extends Controller
{
    /**
     * Display organization settings (Owner Only)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Only owners can access
        if (!$user->isOwner()) {
            abort(403, 'Accès réservé aux propriétaires d\'organisation.');
        }

        $organization = $user->organization;

        // Organization statistics
        $stats = [
            'total_users' => $organization->users()->count(),
            'active_users' => $organization->users()->where('status', 'active')->count(),
            'total_projects' => $organization->projects()->count(),
            'active_projects' => $organization->projects()->where('status', 'active')->count(),
            'total_departments' => $organization->departments()->count(),
            'total_channels' => $organization->channels()->count(),
            'storage_used' => $this->calculateStorageUsed($organization),
            'storage_limit' => $this->getStorageLimit($organization->subscription_plan),
        ];

        return view('settings.index', compact('organization', 'stats'));
    }

    /**
     * Update general information
     */
    public function updateGeneral(Request $request)
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $organization = $user->organization;
        $organization->update($validator->validated());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Informations mises à jour avec succès', 'data' => $organization]);
        }

        return back()->with('success', 'Informations générales mises à jour avec succès.');
    }

    /**
     * Update organization logo
     */
    public function updateLogo(Request $request)
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $organization = $user->organization;

        // Delete old logo if exists
        if ($organization->logo && Storage::exists($organization->logo)) {
            Storage::delete($organization->logo);
        }

        // Store new logo
        $logoPath = $request->file('logo')->store('logos', 'public');
        $organization->update(['logo' => $logoPath]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Logo mis à jour avec succès',
                'logo_url' => Storage::url($logoPath)
            ]);
        }

        return back()->with('success', 'Logo mis à jour avec succès.');
    }

    /**
     * Update branding colors
     */
    public function updateBranding(Request $request)
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $organization = $user->organization;

        // Store branding settings in JSON or separate columns
        $branding = $organization->branding ?? [];
        $branding['primary_color'] = $request->primary_color ?? '#df5526';
        $branding['secondary_color'] = $request->secondary_color ?? '#fbbb2a';
        $branding['accent_color'] = $request->accent_color ?? '#60a5fa';

        $organization->update(['branding' => $branding]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Identité visuelle mise à jour', 'data' => $branding]);
        }

        return back()->with('success', 'Identité visuelle mise à jour avec succès.');
    }

    /**
     * Update advanced settings
     */
    public function updateAdvanced(Request $request)
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'timezone' => 'nullable|string|in:' . implode(',', timezone_identifiers_list()),
            'language' => 'nullable|string|in:fr,en,es,de',
            'date_format' => 'nullable|string|in:d/m/Y,m/d/Y,Y-m-d',
            'time_format' => 'nullable|string|in:H:i,h:i A',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $organization = $user->organization;

        $settings = $organization->settings ?? [];
        $settings['timezone'] = $request->timezone ?? 'Africa/Casablanca';
        $settings['language'] = $request->language ?? 'fr';
        $settings['date_format'] = $request->date_format ?? 'd/m/Y';
        $settings['time_format'] = $request->time_format ?? 'H:i';

        $organization->update(['settings' => $settings]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Paramètres avancés mis à jour', 'data' => $settings]);
        }

        return back()->with('success', 'Paramètres avancés mis à jour avec succès.');
    }

    /**
     * Calculate storage used by organization
     */
    private function calculateStorageUsed(Organization $organization)
    {
        // Calculate total storage (avatars, logos, attachments, etc.)
        $totalSize = 0;

        // User avatars
        foreach ($organization->users as $user) {
            if ($user->avatar && Storage::exists($user->avatar)) {
                $totalSize += Storage::size($user->avatar);
            }
        }

        // Organization logo
        if ($organization->logo && Storage::exists($organization->logo)) {
            $totalSize += Storage::size($organization->logo);
        }

        // Convert to MB
        return round($totalSize / (1024 * 1024), 2);
    }

    /**
     * Get storage limit based on subscription plan
     */
    private function getStorageLimit($plan)
    {
        $limits = [
            'starter' => 1024, // 1GB
            'pro' => 10240, // 10GB
            'business' => 51200, // 50GB
            'enterprise' => 102400, // 100GB
        ];

        return $limits[$plan] ?? 1024;
    }
}

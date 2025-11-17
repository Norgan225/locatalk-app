<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations for super admin (web).
     */
    public function indexWeb(Request $request)
    {
        $user = $request->user();

        // Only super admin can view all organizations
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }

        $organizations = Organization::withCount(['users', 'departments', 'projects'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show organization details (web).
     */
    public function showWeb(Request $request, $id)
    {
        $user = $request->user();

        // Only super admin can view organization details
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }

        $organization = Organization::with(['users', 'departments', 'projects'])
            ->withCount(['users', 'departments', 'projects'])
            ->findOrFail($id);

        return view('organizations.show', compact('organization'));
    }

    /**
     * Display a listing of organizations.
     * - If user is owner (platform owner), return all organizations.
     * - Otherwise return the user's own organization only.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && method_exists($user, 'isOwner') && $user->isOwner()) {
            $items = Organization::orderBy('name')->get();
        } else {
            $items = Organization::where('id', $user?->organization_id)->get();
        }

        if ($request->wantsJson()) {
            return response()->json(['data' => $items], 200);
        }

        return view('organizations.index', ['organizations' => $items]);
    }

    /**
     * Store a newly created organization in storage.
     * Only allowed for owner users.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isOwner') || ! $user->isOwner()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:organizations,slug',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:50',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }

            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();
        $data['created_by'] = $user->id ?? null;

        $org = Organization::create($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Organisation créée.', 'data' => $org], 201);
        }

        return redirect()->route('organization.settings')->with('success', 'Organisation créée.');
    }

    /**
     * Display the specified organization.
     */
    public function show(Request $request, $id)
    {
        $org = Organization::findOrFail($id);
        $user = $request->user();

        // allow owners or users belonging to the organization
        $allowed = ($user && method_exists($user, 'isOwner') && $user->isOwner()) || ($user && $user->organization_id === $org->id);

        if (! $allowed) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        if ($request->wantsJson()) {
            return response()->json(['data' => $org], 200);
        }

        return view('organizations.show', ['organization' => $org]);
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isOwner') || ! $user->isOwner()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        $org = Organization::findOrFail($id);

        $v = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => "nullable|string|max:255|unique:organizations,slug,{$org->id}",
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:50',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }

            return redirect()->back()->withErrors($v)->withInput();
        }

        $org->update($v->validated());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Organisation mise à jour.', 'data' => $org], 200);
        }

        return redirect()->route('organization.settings')->with('success', 'Organisation mise à jour.');
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isOwner') || ! $user->isOwner()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        $org = Organization::findOrFail($id);
        $org->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Organisation supprimée.'], 200);
        }

        return redirect()->route('organization.settings')->with('success', 'Organisation supprimée.');
    }

    /**
     * Show organization settings (web).
     */
    public function settings(Request $request)
    {
        $user = $request->user();
        $org = null;

        if ($user) {
            $org = Organization::find($user->organization_id);
        }

        return view('organization.settings', ['organization' => $org]);
    }

    /**
     * Toggle a simple "remote access" flag on the organization (owner only).
     */
    public function toggleRemoteAccess(Request $request)
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isOwner') || ! $user->isOwner()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : redirect()->back()->with('error', 'Accès refusé.');
        }

        $org = Organization::find($request->input('organization_id'));
        if (! $org) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Organisation introuvable.'], 404)
                : redirect()->back()->with('error', 'Organisation introuvable.');
        }

        // toggle if attribute exists
        if (array_key_exists('remote_access_enabled', $org->getAttributes())) {
            $org->remote_access_enabled = ! $org->remote_access_enabled;
            $org->save();
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Paramètre mis à jour.', 'data' => $org], 200);
        }

        return redirect()->back()->with('success', 'Paramètre mis à jour.');
    }
}

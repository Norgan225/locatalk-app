<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     * - Owners can see all departments across organizations
     * - Admins/users see only departments from their organization
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Department::with(['organization', 'head', 'users']);

        // Filter by organization if not owner
        if ($user && !$user->isOwner()) {
            $query->where('organization_id', $user->organization_id);
        }

        // Optional filters
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $departments = $query->orderBy('name')->get();

        // Add member count to each department
        $departments->each(function ($dept) {
            $dept->members_count = $dept->users()->count();
        });

        if ($request->wantsJson()) {
            return response()->json(['data' => $departments], 200);
        }

        return view('departments.index', ['departments' => $departments]);
    }

    /**
     * Store a newly created department in storage.
     * Only allowed for owner and admin users.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Check permissions (owner or admin)
        if (!$user || !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé. Réservé aux administrateurs.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
            'head_user_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }

            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();

        // If not owner, force organization_id to user's organization
        if (!$user->isOwner()) {
            $data['organization_id'] = $user->organization_id;
        }

        $data['created_by'] = $user->id;
        $data['is_active'] = $data['is_active'] ?? true;

        $department = Department::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Département créé avec succès.',
                'data' => $department->load(['organization', 'head'])
            ], 201);
        }

        return redirect()->route('departments.index')->with('success', 'Département créé avec succès.');
    }

    /**
     * Display the specified department.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $department = Department::with(['organization', 'head', 'users', 'projects', 'channels'])->findOrFail($id);

        // Check access: owner or same organization
        if (!$user->isOwner() && $user->organization_id !== $department->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Add statistics
        $department->statistics = [
            'total_members' => $department->users()->count(),
            'active_projects' => $department->projects()->where('status', 'active')->count(),
            'total_projects' => $department->projects()->count(),
            'channels_count' => $department->channels()->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json(['data' => $department], 200);
        }

        return view('departments.show', ['department' => $department]);
    }

    /**
     * Update the specified department in storage.
     * Only allowed for owner and admin users.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        // Check permissions
        if (!$user || !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé. Réservé aux administrateurs.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $department = Department::findOrFail($id);

        // Non-owners can only edit their own organization's departments
        if (!$user->isOwner() && $user->organization_id !== $department->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $v = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
            'head_user_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $v->errors()], 422);
            }

            return redirect()->back()->withErrors($v)->withInput();
        }

        $department->update($v->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Département mis à jour avec succès.',
                'data' => $department->load(['organization', 'head'])
            ], 200);
        }

        return redirect()->route('departments.show', $department->id)->with('success', 'Département mis à jour.');
    }

    /**
     * Remove the specified department from storage (soft delete).
     * Only allowed for owner and admin users.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        // Check permissions
        if (!$user || !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé. Réservé aux administrateurs.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $department = Department::findOrFail($id);

        // Non-owners can only delete their own organization's departments
        if (!$user->isOwner() && $user->organization_id !== $department->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Check if department has active users
        $usersCount = $department->users()->count();
        if ($usersCount > 0) {
            return $request->wantsJson()
                ? response()->json(['message' => "Impossible de supprimer ce département. Il contient {$usersCount} utilisateur(s)."], 422)
                : redirect()->back()->with('error', "Impossible de supprimer ce département. Il contient {$usersCount} utilisateur(s).");
        }

        $department->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Département supprimé avec succès.'], 200);
        }

        return redirect()->route('departments.index')->with('success', 'Département supprimé avec succès.');
    }

    /**
     * Restore a soft-deleted department.
     * Only allowed for owner and admin users.
     */
    public function restore(Request $request, $id)
    {
        $user = $request->user();

        // Check permissions
        if (!$user || !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $department = Department::withTrashed()->findOrFail($id);

        // Non-owners can only restore their own organization's departments
        if (!$user->isOwner() && $user->organization_id !== $department->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $department->restore();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Département restauré avec succès.',
                'data' => $department
            ], 200);
        }

        return redirect()->route('departments.index')->with('success', 'Département restauré avec succès.');
    }

    /**
     * Toggle department active status.
     * Only allowed for owner and admin users.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = $request->user();

        // Check permissions
        if (!$user || !$user->canManageUsers()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $department = Department::findOrFail($id);

        // Non-owners can only toggle their own organization's departments
        if (!$user->isOwner() && $user->organization_id !== $department->organization_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Accès non autorisé.'], 403)
                : redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $department->is_active = !$department->is_active;
        $department->save();

        $status = $department->is_active ? 'activé' : 'désactivé';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Département {$status} avec succès.",
                'data' => $department
            ], 200);
        }

        return redirect()->back()->with('success', "Département {$status} avec succès.");
    }
}

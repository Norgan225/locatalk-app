<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Vérifier les permissions
        if (!$user->canManageUsers()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $query = User::with(['organization', 'department']);

        // Filtrer par organisation si pas owner
        if (!$user->isOwner()) {
            $query->where('organization_id', $user->organization_id);
        }

        // Filtres optionnels
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15);

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Vérifier les permissions
        if (!$user->canManageUsers()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:owner,admin,responsable,employe',
            'organization_id' => 'nullable|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
        ]);

        // Vérifier que l'organisation appartient à l'utilisateur si pas owner
        if (!$user->isOwner() && $request->organization_id && $request->organization_id != $user->organization_id) {
            return response()->json(['message' => 'Organisation non autorisée'], 403);
        }

        // Générer un mot de passe temporaire
        $tempPassword = Str::random(10);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'password_changed' => false,
            'role' => $request->role,
            'organization_id' => $request->organization_id ?? $user->organization_id,
            'department_id' => $request->department_id,
            'phone' => $request->phone,
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        // Envoyer l'email avec les identifiants
        try {
            Mail::raw("Bienvenue sur LocaTalk !\n\nVos identifiants :\nEmail : {$newUser->email}\nMot de passe temporaire : {$tempPassword}\n\nVeuillez changer votre mot de passe lors de votre première connexion.", function ($message) use ($newUser) {
                $message->to($newUser->email)->subject('Bienvenue sur LocaTalk');
            });
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas échouer la création
            Log::error('Erreur envoi email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $newUser->load(['organization', 'department']),
        ], 201);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        // Vérifier les permissions
        if (!$currentUser->canManageUsers() && $currentUser->id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Vérifier que l'utilisateur appartient à la même organisation si pas owner
        if (!$currentUser->isOwner() && $user->organization_id !== $currentUser->organization_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return response()->json($user->load(['organization', 'department', 'createdUsers']));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        // Vérifier les permissions
        if (!$currentUser->canManageUsers() && $currentUser->id !== $user->id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Vérifier que l'utilisateur appartient à la même organisation si pas owner
        if (!$currentUser->isOwner() && $user->organization_id !== $currentUser->organization_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Empêcher la modification d'un owner si pas owner
        if (!$currentUser->isOwner() && $user->isOwner() && $currentUser->id !== $user->id) {
            return response()->json(['message' => 'Modification d\'un owner non autorisée'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|required|in:owner,admin,responsable,employe',
            'organization_id' => 'sometimes|required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'sometimes|required|in:active,suspended,inactive',
        ]);

        // Empêcher la modification du rôle owner si pas owner
        if (!$currentUser->isOwner() && $request->has('role') && $request->role === 'owner') {
            return response()->json(['message' => 'Modification du rôle owner non autorisée'], 403);
        }

        // Vérifier l'organisation si modifiée
        if ($request->has('organization_id') && !$currentUser->isOwner() && $request->organization_id != $currentUser->organization_id) {
            return response()->json(['message' => 'Organisation non autorisée'], 403);
        }

        $user->update($request->only([
            'name', 'email', 'role', 'organization_id', 'department_id', 'phone', 'status'
        ]));

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user->load(['organization', 'department']),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        // Vérifier les permissions
        if (!$currentUser->canManageUsers()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Vérifier que l'utilisateur appartient à la même organisation si pas owner
        if (!$currentUser->isOwner() && $user->organization_id !== $currentUser->organization_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Empêcher la suppression de soi-même
        if ($currentUser->id === $user->id) {
            return response()->json(['message' => 'Impossible de se supprimer soi-même'], 400);
        }

        // Empêcher la suppression d'un owner si pas owner
        if (!$currentUser->isOwner() && $user->isOwner()) {
            return response()->json(['message' => 'Suppression d\'un owner non autorisée'], 403);
        }

        $user->delete(); // Soft delete

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    public function restore(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user();

        // Vérifier les permissions
        if (!$currentUser->canManageUsers()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $user = User::withTrashed()->findOrFail($id);

        // Vérifier que l'utilisateur appartient à la même organisation si pas owner
        if (!$currentUser->isOwner() && $user->organization_id !== $currentUser->organization_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $user->restore();

        return response()->json([
            'message' => 'Utilisateur restauré avec succès',
            'user' => $user->load(['organization', 'department']),
        ]);
    }

    // ===== WEB METHODS FOR BLADE VIEWS =====

    public function indexWeb(Request $request)
    {
        $user = $request->user();

        // Vérifier les permissions
        if (!$user->canManageUsers()) {
            abort(403, 'Accès non autorisé');
        }

        $query = User::with(['organization', 'department']);

        // Filtrer par organisation si pas super_admin
        if (!$user->isSuperAdmin()) {
            $query->where('organization_id', $user->organization_id);
        }

        // Filtres optionnels
        if ($request->has('organization_id') && $request->organization_id) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(20);

        // Organizations: super_admin voit tout, autres voient leur org
        $organizations = $user->isSuperAdmin()
            ? Organization::all()
            : Organization::where('id', $user->organization_id)->get();

        $departments = Department::where('organization_id', $user->organization_id)->get();

        return view('users.index', compact('users', 'organizations', 'departments'));
    }

    public function showWeb(Request $request, $id)
    {
        $currentUser = $request->user();
        $user = User::with(['organization', 'department', 'createdUsers', 'createdProjects', 'assignedTasks'])->findOrFail($id);

        // Vérifier les permissions
        if (!$currentUser->canManageUsers() && $currentUser->id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que l'utilisateur appartient à la même organisation si pas super_admin
        if (!$currentUser->isSuperAdmin() && $user->organization_id !== $currentUser->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        return view('users.show', compact('user'));
    }

    public function storeWeb(Request $request)
    {
        $user = $request->user();

        // Vérifier les permissions
        if (!$user->canManageUsers()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,owner,admin,responsable,employe',
            'organization_id' => 'nullable|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
        ]);

        // Vérifier que l'organisation appartient à l'utilisateur si pas super_admin
        if (!$user->isSuperAdmin() && $request->organization_id && $request->organization_id != $user->organization_id) {
            abort(403, 'Organisation non autorisée');
        }

        // Empêcher la création de super_admin si pas déjà super_admin
        if ($request->role === 'super_admin' && !$user->isSuperAdmin()) {
            abort(403, 'Vous ne pouvez pas créer un super admin');
        }

        // Générer un mot de passe temporaire
        $tempPassword = Str::random(10);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'password_changed' => false,
            'role' => $request->role,
            'organization_id' => $request->organization_id ?? $user->organization_id,
            'department_id' => $request->department_id,
            'phone' => $request->phone,
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'action' => 'user_created',
            'description' => "Utilisateur {$newUser->name} créé",
            'model_type' => 'User',
            'model_id' => $newUser->id,
        ]);

        // Envoyer l'email avec les identifiants
        try {
            Mail::raw("Bienvenue sur LocaTalk !\n\nVos identifiants :\nEmail : {$newUser->email}\nMot de passe temporaire : {$tempPassword}\n\nVeuillez changer votre mot de passe lors de votre première connexion.", function ($message) use ($newUser) {
                $message->to($newUser->email)->subject('Bienvenue sur LocaTalk');
            });
        } catch (\Exception $e) {
            Log::error('Erreur envoi email: ' . $e->getMessage());
        }

        return redirect()->route('web.users')->with('success', 'Utilisateur créé avec succès. Email envoyé avec mot de passe temporaire.');
    }

    public function updateWeb(Request $request, $id)
    {
        $currentUser = $request->user();
        $user = User::findOrFail($id);

        // Vérifier les permissions
        if (!$currentUser->canManageUsers() && $currentUser->id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que l'utilisateur appartient à la même organisation si pas super_admin
        if (!$currentUser->isSuperAdmin() && $user->organization_id !== $currentUser->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|required|in:super_admin,owner,admin,responsable,employe',
            'organization_id' => 'sometimes|required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'sometimes|required|in:active,suspended,inactive',
        ]);

        // Empêcher la modification du rôle super_admin si pas super_admin
        if (!$currentUser->isSuperAdmin() && $request->has('role') && $request->role === 'super_admin') {
            abort(403, 'Modification du rôle super_admin non autorisée');
        }

        // Vérifier l'organisation si modifiée
        if ($request->has('organization_id') && !$currentUser->isSuperAdmin() && $request->organization_id != $currentUser->organization_id) {
            abort(403, 'Organisation non autorisée');
        }

        $user->update($request->only([
            'name', 'email', 'role', 'organization_id', 'department_id', 'phone', 'status'
        ]));

        // Log activity
        ActivityLog::create([
            'user_id' => $currentUser->id,
            'organization_id' => $currentUser->organization_id,
            'action' => 'user_updated',
            'description' => "Utilisateur {$user->name} mis à jour",
            'model_type' => 'User',
            'model_id' => $user->id,
        ]);

        return redirect()->route('web.users.show', $user->id)->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroyWeb(Request $request, $id)
    {
        $currentUser = $request->user();
        $user = User::findOrFail($id);

        // Vérifier les permissions
        if (!$currentUser->canManageUsers()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que l'utilisateur appartient à la même organisation si pas super_admin
        if (!$currentUser->isSuperAdmin() && $user->organization_id !== $currentUser->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Empêcher la suppression de soi-même
        if ($currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'Impossible de se supprimer soi-même');
        }

        // Empêcher la suppression d'un super_admin si pas super_admin
        if (!$currentUser->isSuperAdmin() && $user->isSuperAdmin()) {
            abort(403, 'Suppression d\'un super_admin non autorisée');
        }

        $userName = $user->name;
        $user->delete(); // Soft delete

        // Log activity
        ActivityLog::create([
            'user_id' => $currentUser->id,
            'organization_id' => $currentUser->organization_id,
            'action' => 'user_deleted',
            'description' => "Utilisateur {$userName} supprimé",
            'model_type' => 'User',
            'model_id' => $user->id,
        ]);

        return redirect()->route('web.users')->with('success', 'Utilisateur supprimé avec succès');
    }

    public function restoreWeb(Request $request, $id)
    {
        $currentUser = $request->user();

        // Vérifier les permissions
        if (!$currentUser->canManageUsers()) {
            abort(403, 'Accès non autorisé');
        }

        $user = User::withTrashed()->findOrFail($id);

        // Vérifier que l'utilisateur appartient à la même organisation si pas super_admin
        if (!$currentUser->isSuperAdmin() && $user->organization_id !== $currentUser->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        $user->restore();

        // Log activity
        ActivityLog::create([
            'user_id' => $currentUser->id,
            'organization_id' => $currentUser->organization_id,
            'action' => 'user_restored',
            'description' => "Utilisateur {$user->name} restauré",
            'model_type' => 'User',
            'model_id' => $user->id,
        ]);

        return redirect()->route('web.users.show', $user->id)->with('success', 'Utilisateur restauré avec succès');
    }

    /**
     * Obtenir la clé publique d'un utilisateur pour E2E encryption
     */
    public function getPublicKey(Request $request, $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);

            // Vérifier que l'utilisateur a une clé publique
            if (!$user->e2e_public_key) {
                return response()->json(['message' => 'Clé publique non trouvée'], 404);
            }

            return response()->json([
                'public_key' => $user->e2e_public_key
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération clé publique E2E', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Établir une connexion E2E avec un utilisateur
     */
    public function establishE2EConnection(Request $request, $userId): JsonResponse
    {
        try {
            $user = $request->user();
            $targetUser = User::findOrFail($userId);

            $request->validate([
                'public_key' => 'required|string',
                'encrypted_secret' => 'required|string'
            ]);

            // Sauvegarder la clé publique de l'utilisateur actuel si pas déjà fait
            if (!$user->e2e_public_key) {
                $user->update(['e2e_public_key' => $request->public_key]);
            }

            // Ici on pourrait stocker les secrets partagés dans une table dédiée
            // Pour l'instant, on confirme juste l'établissement de la connexion

            Log::info('Connexion E2E établie', [
                'from_user' => $user->id,
                'to_user' => $userId
            ]);

            return response()->json([
                'message' => 'Connexion E2E établie avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur établissement connexion E2E', [
                'from_user' => $request->user()->id,
                'to_user' => $userId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }
}

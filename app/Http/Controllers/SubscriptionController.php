<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display plans and subscriptions overview (super admin only).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Only super admin can access
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }

        // Available plans with their features
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 0,
                'max_users' => 5,
                'features' => [
                    '5 utilisateurs',
                    '3 projets',
                    '5 Go de stockage',
                    'Support email',
                ],
                'color' => 'purple',
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 29000,
                'max_users' => 50,
                'features' => [
                    '50 utilisateurs',
                    'Projets illimités',
                    '100 Go de stockage',
                    'Support prioritaire',
                    'Rapports avancés',
                ],
                'color' => 'blue',
                'popular' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'price' => 79000,
                'max_users' => 200,
                'features' => [
                    '200 utilisateurs',
                    'Projets illimités',
                    '500 Go de stockage',
                    'Support prioritaire 24/7',
                    'Rapports avancés',
                    //'API personnalisée',
                ],
                'color' => 'green',
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => null, // Custom pricing
                'max_users' => null, // Unlimited
                'features' => [
                    'Utilisateurs illimités',
                    'Projets illimités',
                    'Stockage illimité',
                    'Support dédié 24/7',
                    'Rapports personnalisés',
                    //'API complète',
                    //'SLA garantie',
                    'Formation équipe',
                ],
                'color' => 'gradient',
            ],
        ];

        // Get subscriptions statistics
        $organizations = Organization::all();
        $subscriptions = [
            'total' => $organizations->count(),
            'active' => $organizations->where('subscription_status', 'active')->count(),
            'trial' => $organizations->where('subscription_status', 'trial')->count(),
            'expired' => $organizations->where('subscription_status', 'expired')->count(),
            'by_plan' => [
                'starter' => $organizations->where('plan', 'starter')->count(),
                'pro' => $organizations->where('plan', 'pro')->count(),
                'business' => $organizations->where('plan', 'business')->count(),
                'enterprise' => $organizations->where('plan', 'enterprise')->count(),
            ],
        ];

        // Recent subscriptions
        $recentSubscriptions = Organization::with('users')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('subscriptions.index', compact('plans', 'subscriptions', 'recentSubscriptions'));
    }

    /**
     * Update organization plan.
     */
    public function updatePlan(Request $request, $organizationId)
    {
        $user = $request->user();

        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $validated = $request->validate([
            'plan' => 'required|in:starter,pro,business,enterprise',
            'max_users' => 'nullable|integer|min:1',
        ]);

        $organization = Organization::findOrFail($organizationId);
        $organization->update([
            'plan' => $validated['plan'],
            'max_users' => $validated['max_users'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Plan mis à jour avec succès !');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Créer une transaction pour assurer la cohérence
        DB::beginTransaction();

        try {
            // Créer automatiquement une organisation pour le nouvel utilisateur
            $organization = Organization::create([
                'name' => $request->name . "'s Organization",
                'slug' => \Illuminate\Support\Str::slug($request->name . '-org-' . time()),
                'plan' => 'free',
                'subscription_status' => 'active',
                'max_users' => 10,
            ]);

            // Créer l'utilisateur avec le rôle Owner et l'associer à son organisation
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'organization_id' => $organization->id,
                'role' => 'owner', // Celui qui s'inscrit devient owner de son organisation
            ]);

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

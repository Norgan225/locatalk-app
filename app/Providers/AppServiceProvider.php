<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Permission pour gérer les utilisateurs et canaux
        Gate::define('manage-users', function (User $user) {
            return $user->canManageUsers();
        });

        // Permission pour gérer les départements
        Gate::define('manage-departments', function (User $user) {
            return $user->canManageDepartments();
        });

        // Permission pour voir toutes les organisations
        Gate::define('see-all-organizations', function (User $user) {
            return $user->canSeeAllOrganizations();
        });
    }
}

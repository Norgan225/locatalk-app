<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Channel;
use App\Observers\UserObserver;
use App\Observers\ChannelObserver;

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
        // Forcer HTTPS pour les assets et URLs si derrière un proxy (comme ngrok)
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            request()->server('HTTPS') === 'on' ||
            env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Observers pour auto-ajouter les utilisateurs aux canaux de département
        User::observe(UserObserver::class);
        Channel::observe(ChannelObserver::class);

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

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware globaux (s'appliquent à toutes les requêtes)
        $middleware->append(\App\Http\Middleware\TrackActivity::class);
        $middleware->append(\App\Http\Middleware\TrackUserActivity::class);

        // Middleware nommés (à utiliser dans les routes)
        $middleware->alias([
            'check.mac' => \App\Http\Middleware\CheckMacAddress::class,
            'check.owner' => \App\Http\Middleware\CheckOwner::class,
            'super.admin' => \App\Http\Middleware\CheckSuperAdmin::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'responsable' => \App\Http\Middleware\CheckResponsable::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

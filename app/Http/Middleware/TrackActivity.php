<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class TrackActivity
{
    /**
     * Enregistrer les activités des utilisateurs
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Enregistrer seulement si l'utilisateur est authentifié
        if ($user = $request->user()) {
            // Ne pas logger les requêtes AJAX répétées
            if (!$request->ajax() || $this->isImportantAction($request)) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'organization_id' => $user->organization_id,
                    'action' => $this->getActionName($request),
                    'description' => $this->getDescription($request),
                    'ip_address' => $request->ip(),
                    'device_fingerprint' => $request->input('device_fingerprint'),
                    'metadata' => [
                        'method' => $request->method(),
                        'url' => $request->fullUrl(),
                        'user_agent' => $request->userAgent(),
                    ],
                ]);
            }
        }

        return $response;
    }

    /**
     * Obtenir le nom de l'action
     */
    private function getActionName(Request $request): string
    {
        $route = $request->route();

        if (!$route) {
            return 'page_view';
        }

        $routeName = $route->getName();

        // Mapper les noms de routes aux actions
        $actions = [
            'login' => 'login',
            'logout' => 'logout',
            'users.store' => 'user_created',
            'users.update' => 'user_updated',
            'users.destroy' => 'user_deleted',
            'departments.store' => 'department_created',
            'projects.store' => 'project_created',
            'messages.send' => 'message_sent',
            'meetings.store' => 'meeting_created',
        ];

        return $actions[$routeName] ?? 'page_view';
    }

    /**
     * Obtenir une description de l'action
     */
    private function getDescription(Request $request): ?string
    {
        $route = $request->route();

        if (!$route) {
            return null;
        }

        $action = $this->getActionName($request);

        switch ($action) {
            case 'user_created':
                return 'Nouvel utilisateur créé : ' . $request->input('name');
            case 'department_created':
                return 'Nouveau département créé : ' . $request->input('name');
            case 'project_created':
                return 'Nouveau projet créé : ' . $request->input('name');
            default:
                return null;
        }
    }

    /**
     * Déterminer si c'est une action importante à logger
     */
    private function isImportantAction(Request $request): bool
    {
        $importantRoutes = [
            'users.store', 'users.update', 'users.destroy',
            'departments.store', 'departments.update', 'departments.destroy',
            'projects.store', 'projects.update',
            'messages.send',
            'meetings.store',
        ];

        $route = $request->route();
        return $route && in_array($route->getName(), $importantRoutes);
    }
}

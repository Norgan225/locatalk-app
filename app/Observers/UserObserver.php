<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Channel;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Ajouter automatiquement l'utilisateur aux canaux de son département
     */
    public function created(User $user): void
    {
        $this->addToDepartmentChannels($user);
    }

    /**
     * Handle the User "updated" event.
     * Si le département change, mettre à jour les canaux
     */
    public function updated(User $user): void
    {
        // Vérifier si le département a changé
        if ($user->isDirty('department_id')) {
            $oldDepartmentId = $user->getOriginal('department_id');
            $newDepartmentId = $user->department_id;

            // Retirer des anciens canaux de département
            if ($oldDepartmentId) {
                $this->removeFromDepartmentChannels($user, $oldDepartmentId);
            }

            // Ajouter aux nouveaux canaux de département
            if ($newDepartmentId) {
                $this->addToDepartmentChannels($user);
            }
        }
    }

    /**
     * Ajouter l'utilisateur à tous les canaux de type "department" de son département
     */
    protected function addToDepartmentChannels(User $user): void
    {
        if (!$user->department_id || !$user->organization_id) {
            return;
        }

        // Trouver tous les canaux de type "department" pour ce département
        $departmentChannels = Channel::where('type', 'department')
            ->where('department_id', $user->department_id)
            ->where('organization_id', $user->organization_id)
            ->get();

        foreach ($departmentChannels as $channel) {
            // Ajouter l'utilisateur s'il n'est pas déjà membre
            if (!$channel->users()->where('user_id', $user->id)->exists()) {
                $channel->users()->attach($user->id);

                // Logger l'activité
                \App\Models\ActivityLog::log(
                    'auto_channel_join',
                    "Utilisateur {$user->name} auto-ajouté au canal {$channel->name}",
                    $user->id
                );
            }
        }
    }

    /**
     * Retirer l'utilisateur des canaux d'un département
     */
    protected function removeFromDepartmentChannels(User $user, int $departmentId): void
    {
        if (!$user->organization_id) {
            return;
        }

        // Trouver tous les canaux de type "department" pour l'ancien département
        $departmentChannels = Channel::where('type', 'department')
            ->where('department_id', $departmentId)
            ->where('organization_id', $user->organization_id)
            ->get();

        foreach ($departmentChannels as $channel) {
            // Ne pas retirer si l'utilisateur est le créateur du canal
            if ($channel->created_by !== $user->id) {
                $channel->users()->detach($user->id);

                // Logger l'activité
                \App\Models\ActivityLog::log(
                    'auto_channel_leave',
                    "Utilisateur {$user->name} auto-retiré du canal {$channel->name} (changement de département)",
                    $user->id
                );
            }
        }
    }
}

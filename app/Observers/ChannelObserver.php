<?php

namespace App\Observers;

use App\Models\Channel;
use App\Models\User;

class ChannelObserver
{
    /**
     * Handle the Channel "created" event.
     * Pour les canaux de type "department", ajouter automatiquement tous les utilisateurs du département
     */
    public function created(Channel $channel): void
    {
        if ($channel->type === 'department' && $channel->department_id) {
            $this->addDepartmentMembers($channel);
        }
    }

    /**
     * Handle the Channel "updated" event.
     */
    public function updated(Channel $channel): void
    {
        // Si le type change vers "department" ou si le département change
        if ($channel->isDirty('type') || $channel->isDirty('department_id')) {
            $oldType = $channel->getOriginal('type');
            $newType = $channel->type;
            $oldDepartmentId = $channel->getOriginal('department_id');
            $newDepartmentId = $channel->department_id;

            // Si on change vers un canal de département
            if ($newType === 'department' && $newDepartmentId) {
                // Retirer les anciens membres du département si le département a changé
                if ($oldType === 'department' && $oldDepartmentId && $oldDepartmentId !== $newDepartmentId) {
                    $this->removeDepartmentMembers($channel, $oldDepartmentId);
                }

                // Ajouter les nouveaux membres du département
                $this->addDepartmentMembers($channel);
            }
            // Si on change depuis un canal de département vers un autre type
            elseif ($oldType === 'department' && $newType !== 'department') {
                // On ne retire pas automatiquement les membres
                // Le créateur peut gérer cela manuellement
            }
        }
    }

    /**
     * Ajouter tous les utilisateurs du département au canal
     */
    protected function addDepartmentMembers(Channel $channel): void
    {
        if (!$channel->department_id || !$channel->organization_id) {
            return;
        }

        // Trouver tous les utilisateurs du département dans la même organisation
        $users = User::where('department_id', $channel->department_id)
            ->where('organization_id', $channel->organization_id)
            ->where('status', 'active')
            ->get();

        foreach ($users as $user) {
            // Ajouter l'utilisateur s'il n'est pas déjà membre
            if (!$channel->users()->where('user_id', $user->id)->exists()) {
                $channel->users()->attach($user->id);
            }
        }

        // Logger l'activité
        \App\Models\ActivityLog::log(
            'department_channel_created',
            "Canal département {$channel->name} créé avec {$users->count()} membres auto-ajoutés"
        );
    }

    /**
     * Retirer les utilisateurs d'un ancien département
     */
    protected function removeDepartmentMembers(Channel $channel, int $oldDepartmentId): void
    {
        // Trouver les utilisateurs de l'ancien département
        $oldDepartmentUserIds = User::where('department_id', $oldDepartmentId)
            ->where('organization_id', $channel->organization_id)
            ->pluck('id')
            ->toArray();

        // Retirer ces utilisateurs du canal (sauf le créateur)
        $channel->users()
            ->whereIn('user_id', $oldDepartmentUserIds)
            ->where('user_id', '!=', $channel->created_by)
            ->detach();
    }
}

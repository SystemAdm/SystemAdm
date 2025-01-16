<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;

class EventService
{
    /**
     * Håndter brukere ved å fjerne dem fra spesifikke relasjoner.
     *
     * @param Event $event
     * @param User $user
     * @param array $relations
     */
    public function detachRelations(Event $event, User $user, array $relations): void
    {
        foreach ($relations as $relation) {
            if ($event->$relation()->where('user_id', $user->id)->exists()) {
                $event->$relation()->detach($user->id);
            }
        }
    }

    /**
     * Legg brukeren til i en relasjon om eksisterer.
     *
     * @param Event $event
     * @param User $user
     * @param string $relation
     */
    public function attachToRelationIfNotExists(Event $event, User $user, string $relation): void
    {
        if (!$event->$relation()->where('user_id', $user->id)->exists()) {
            $event->$relation()->attach($user->id);
        }
    }
}

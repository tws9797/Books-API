<?php

namespace App\Policies;

use App\User;
use App\Publisher;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublisherPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the publisher.
     *
     * @param  \App\User  $user
     * @param  \App\Publisher  $publisher
     * @return mixed
     */
    public function view(User $user, Publisher $publisher)
    {
        return true;
    }

    /**
     * Determine whether the user can create publishers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->name === 'Administrator';
    }

    /**
     * Determine whether the user can update the publisher.
     *
     * @param  \App\User  $user
     * @param  \App\Publisher  $publisher
     * @return mixed
     */
    public function update(User $user, Publisher $publisher)
    {
        return $user->name === 'Administrator';
    }

    /**
     * Determine whether the user can delete the publisher.
     *
     * @param  \App\User  $user
     * @param  \App\Publisher  $publisher
     * @return mixed
     */
    public function delete(User $user, Publisher $publisher)
    {
        return $user->name === 'Administrator';
    }
}

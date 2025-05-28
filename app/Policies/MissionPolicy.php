<?php


namespace App\Policies;

use App\Models\Mission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MissionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Mission $mission)
    {
        return $user->id === $mission->user_id;
    }

    public function update(User $user, Mission $mission)
    {
        return $user->id === $mission->user_id && $mission->status === 'soumise';
    }

    public function delete(User $user, Mission $mission)
    {
        return $user->id === $mission->user_id && $mission->status === 'soumise';
    }
}
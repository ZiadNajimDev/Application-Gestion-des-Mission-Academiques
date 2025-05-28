<?php

namespace App\Policies;

use App\Models\MissionProof;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MissionProofPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the proof.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MissionProof  $proof
     * @return bool
     */
    public function view(User $user, MissionProof $proof)
    {
        return $user->id === $proof->mission->user_id;
    }

    /**
     * Determine whether the user can delete the proof.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MissionProof  $proof
     * @return bool
     */
    public function delete(User $user, MissionProof $proof)
    {
        return $user->id === $proof->mission->user_id && $proof->status === 'pending';
    }
}
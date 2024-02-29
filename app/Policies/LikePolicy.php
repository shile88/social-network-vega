<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LikePolicy
{
    public function before(User $user, $ability)
    {
        if($user->is_admin)
            return true;
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Like $like): Response
    {
        return $user->id === $like->user_id
            ? Response::allow()
            : Response::deny(
                'Not your like.');
    }
}

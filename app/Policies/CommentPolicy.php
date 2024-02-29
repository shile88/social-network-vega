<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CommentPolicy
{
    public function before(User $user, $ability)
    {
        if($user->is_admin)
            return true;
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): Response
    {
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny(
                'Not your comment.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): Response
    {
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny(
                'Not your comment.');
    }

}

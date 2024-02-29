<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    public function before(User $user, $ability)
    {
        if($user->is_admin)
            return true;
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): Response
    {
        //Get all friends of current user where status is accepted and give permission to see post
        $friendsOfMine = $user->friendsOfMine->pluck('id')->toArray();
        $friendsOf = $user->friendOf->pluck('id')->toArray();

        if (
            in_array($post->user_id, $friendsOfMine) ||
            in_array($post->user_id, $friendsOf) ||
            $user->id == $post->user_id
        ) {
            return Response::allow();
        } else {
            return Response::deny(
                'Not yours or your friends post.');
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
            ? Response::allow()
            : Response::deny(
                'Not your post.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
            ? Response::allow()
            : Response::deny(
                'Not your post.');
    }
}

<?php

namespace App\Services;

use App\Models\Like;
use App\Notifications\NewLikeNotification;

class LikeService
{
    /**
     * Get paginated likes for a specific post.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index($post)
    {
        return $post->likes()->paginate(10);
    }

    /**
     * Store a new like for a specific post.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store($post)
    {
        //Create new like with validated data
        $like = $post->likes()->firstOrCreate(['user_id' => auth()->user()->id]);

        // Notify the post owner about the new like
        $post->user->notify(new NewLikeNotification(auth()->user(), $post));

        return $like;
    }

    /**
     * Get a specific like for a post.
     *
     * @param \App\Models\Post $post
     * @param \App\Models\Like $like
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function show($post, $like)
    {
        return Like::query()->where('post_id', $post->id)
            ->where('id', $like->id)
            ->firstOrFail();
    }

    /**
     * Delete a specific like for a post.
     *
     * @param \App\Models\Post $post
     * @param \App\Models\Like $like
     * @return bool|null
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function destroy($post, $like)
    {
        $like = Like::query()->where('post_id', $post->id)
            ->where('id', $like->id)
            ->firstOrFail();

       return $like->delete();
    }

}

<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class ReplyService
{
    /**
     * Get paginated replies for a specific comment, cached for the authenticated user.
     *
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index($comment)
    {
        // Cache the paginated replies for the authenticated user and a specific comment.
        return Cache::rememberForever(
            'user_' . auth()->id() . '_comment_' . $comment->id . '_replies',
            function () use ($comment) {
                return auth()->user()->comments()
                    ->where('parent_id', $comment->id)
                    ->paginate(10);
        });
    }

    /**
     * Store a new reply for a specific post and comment, and clear the associated cache.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store($request, $post, $comment)
    {
        $validatedData = $request->validated();

        // Create a new comment as a reply to a specific post and comment.
        $newComment = Comment::query()->create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'parent_id' => $comment->id,
            'content' => $validatedData['content']
        ]);

        // Clear the cached replies associated with the authenticated user and the parent comment.
        Cache::forget('user_' . auth()->id() . '_comment_' . $comment->id . '_replies');

        return $newComment;
    }
}

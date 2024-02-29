<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewCommentNotification;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CommentService
{
    /**
     * Get paginated comments for a specific post.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index($post)
    {
       return $post->comments()->paginate(5);
    }

    /**
     * Store a new comment for a specific post.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store($request, $post)
    {
        //Create new comment with validated data
        $validatedData = $request->validated();
        $newComment = Comment::query()->create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'content' => $validatedData['content']
        ]);

        // Notify the post owner about the new comment
        $post->user->notify(new NewCommentNotification(auth()->user(), $post, $newComment));

        return $newComment;
    }

    /**
     * Get a specific comment for a post.
     *
     * @param \App\Models\Post $post
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function show($post, $comment)
    {
        return Comment::query()->where('post_id', $post->id)
            ->where('id', $comment->id)
            ->firstOrFail();
    }

    /**
     * Update a specific comment for a post.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function update($request, $post, $comment)
    {
        $validatedData = $request->validated();

        $comment = Comment::query()->where('post_id', $post->id)
            ->where('id', $comment->id)
            ->firstOrFail();

        // Update the post content
        $comment->update([
            'content' => $validatedData['content'],
        ]);

        return $comment;
    }

    /**
     * Delete a specific comment for a post.
     *
     * @param \App\Models\Post $post
     * @param \App\Models\Comment $comment
     * @return bool|null
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function destroy($post, $comment)
    {
        $comment = Comment::query()->where('post_id', $post->id)
            ->where('id', $comment->id)
            ->firstOrFail();

        return $comment->delete();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
class CommentController extends Controller
{
    /**
     * CommentController constructor.
     *
     * @param \App\Services\CommentService $commentService The comment service.
     */
    public function __construct(protected CommentService $commentService)
    {
    }

    /**
     * Display a listing of comments for a specific post.
     *
     * @param \App\Models\Post $post The post for which comments are retrieved.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing comments.
     * @throws AuthorizationException
     */
    public function index(Post $post) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        // Retrieve comments for the post using the CommentService
        $comments = $this->commentService->index($post);

        // Return a JSON response with comments and pagination information
        return response()->json([
            'success' => true,
            'message' => 'List of your comments',
            'data' => [
                'comments' => CommentResource::collection($comments),
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created comment for a specific post.
     *
     * @param \App\Http\Requests\StoreCommentRequest $request The request containing comment data.
     * @param \App\Models\Post $post The post for which the comment is created.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the comment creation.
     * @throws AuthorizationException
     */
    public function store(StoreCommentRequest $request, Post $post) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        // Create a new comment using the CommentService
        $comment = $this->commentService->store($request, $post);

        // Return a JSON response with the created comment
        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully',
            'data' => [
                'comment' => CommentResource::make($comment)
            ]
        ], ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display a specific comment for a post.
     *
     * @param \App\Models\Post $post The post associated with the comment.
     * @param \App\Models\Comment $comment The comment to be displayed.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the specified comment.
     * @throws AuthorizationException
     */
    public function show(Post $post, Comment $comment) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        // Retrieve the specified comment using the CommentService
        $comment = $this->commentService->show($post, $comment);

        // Return a JSON response with the comment
        return response()->json([
            'success' => true,
            'message' => 'Comment for your post',
            'data' => [
                'comment' => CommentResource::make($comment),
            ]
        ]);
    }

    /**
     * Update a specific comment for a post.
     *
     * @param \App\Http\Requests\UpdateCommentRequest $request The request containing updated comment data.
     * @param \App\Models\Post $post The post associated with the comment.
     * @param \App\Models\Comment $comment The comment to be updated.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the comment update.
     * @throws AuthorizationException
     */
    public function update(UpdateCommentRequest $request, Post $post, Comment $comment) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);
        //Use CommentPolicy to authorize can you update comment
        $this->authorize('update', $comment);

        // Update the comment using the CommentService
        $updatedComment = $this->commentService->update($request, $post, $comment);

        // Return a JSON response with the updated comment
        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => CommentResource::make($updatedComment)
        ]);
    }

    /**
     * Remove a specific comment from storage.
     *
     * @param \App\Models\Post $post The post associated with the comment.
     * @param \App\Models\Comment $comment The comment to be deleted.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the comment deletion.
     * @throws AuthorizationException
     */
    public function destroy(Post $post, Comment $comment) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);
        //Use CommentPolicy to authorize can you delete comment
        $this->authorize('delete', $comment);

        // Delete the comment using the CommentService
        $this->commentService->destroy($post, $comment);

        // Return a JSON response indicating the success of the comment deletion
        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
            'data' => null
        ]);
    }
}

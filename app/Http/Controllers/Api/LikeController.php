<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LikeResource;
use App\Models\Like;
use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LikeController extends Controller
{
    /**
     * LikeController constructor.
     *
     * @param \App\Services\LikeService $likeService The like service.
     */
    public function __construct(protected LikeService $likeService)
    {
    }

    /**
     * Display a listing of likes for a specific post.
     *
     * @param \App\Models\Post $post The post for which likes are retrieved.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing likes for the post.
     */
    public function index(Post $post) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        // Retrieve likes for the post using the LikeService
        $likes = $this->likeService->index($post);

        // Return a JSON response with likes and pagination information
        return response()->json([
            'success' => true,
            'message' => 'All likes for post',
            'data' => [
                'likes' => LikeResource::collection($likes),
                'pagination' => [
                    'current_page' => $likes->currentPage(),
                    'last_page' => $likes->lastPage(),
                    'per_page' => $likes->perPage(),
                    'total' => $likes->total(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created like for a specific post.
     *
     * @param \App\Models\Post $post The post for which the like is created.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the like creation.
     */
    public function store(Post $post) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        // Create a new like using the LikeService
        $like = $this->likeService->store($post);

        // Check if the like was recently created and return the appropriate response
        return $like->wasRecentlyCreated ?
                response()->json([
                    'success' => true,
                    'message' => 'Like created successfully',
                    'data' => [
                        'like' => LikeResource::make($like)
                    ]
                ], ResponseAlias::HTTP_CREATED)
           :
                response()->json([
                    'success' => false,
                    'message' => 'User already liked the post',
                ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Display the specified like for a post.
     *
     * @param \App\Models\Post $post The post associated with the like.
     * @param \App\Models\Like $like The like to be displayed.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the specified like.
     * @throws AuthorizationException
     */
    public function show(Post $post, Like $like) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        // Retrieve and return the specified like using the LikeService
        $like = $this->likeService->show($post, $like);

        return response()->json([
            'success' => true,
            'message' => 'Your like',
            'data' => [
                'like' => LikeResource::make($like),
            ]
        ]);
    }

    /**
     * Remove the specified like from storage.
     *
     * @param \App\Models\Post $post The post associated with the like.
     * @param \App\Models\Like $like The like to be deleted.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the like deletion.
     * @throws AuthorizationException
     */
    public function destroy(Post $post, Like $like)
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        //Use LikePolicy to authorize can you delete like
        $this->authorize('delete', $like);

        // Delete the like using the LikeService
        $this->likeService->destroy($post, $like);

        // Return a JSON response indicating the success of the like deletion
        return response()->json([
            'success' => true,
            'message' => 'Like deleted successfully']);

    }
}

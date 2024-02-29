<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PostController extends Controller
{
    /**
     * PostController constructor.
     *
     * @param \App\Services\PostService $postService The post service.
     */
    public function __construct(protected PostService $postService)
    {
    }

    /**
     * Display a listing of the authenticated user's posts.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the user's posts.
     */
    public function index() : JsonResponse
    {
        // Retrieve the authenticated user's posts using the PostService
        $posts = $this->postService->index();

        // Return a JSON response with the user's posts and pagination information
        return response()->json([
            'success' => true,
            'message' => 'All my posts',
            'data' => [
                'posts' => PostResource::collection($posts),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created post in storage.
     *
     * @param \App\Http\Requests\StorePostRequest $request The request containing post data.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of post creation.
     */
    public function store(StorePostRequest $request) : JsonResponse
    {
        // Create a new post using the PostService
        $post = $this->postService->store($request);

        // Return a JSON response indicating the success of post creation
        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => [
                'post' => PostResource::make($post)
            ]
        ], ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified post.
     *
     * @param \App\Models\Post $post The post to be displayed.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the specified post.
     * @throws AuthorizationException
     */
    public function show(Post $post) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('view', $post);

        // Retrieve and return the specified post using the PostService
       $showPost = $this->postService->show($post);

        // Return a JSON response indicating the success of post show
       return response()->json([
           'success' => true,
           'message' => 'Your post',
           'data' => [
               'post' => PostResource::make($showPost)
           ]
       ]);
    }

    /**
     * Update the specified post in storage.
     *
     * @param \App\Http\Requests\UpdatePostRequest $request The request containing updated post data.
     * @param \App\Models\Post $post The post to be updated.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of post update.
     * @throws AuthorizationException
     */
    public function update(UpdatePostRequest $request, Post $post) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('update', $post);

        // Update the specified post using the PostService
        $updatedPost = $this->postService->update($request, $post);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => [
                'post' => PostResource::make($updatedPost)
            ]
        ]);
    }


    /**
     * Remove the specified post from storage.
     *
     * @param \App\Models\Post $post The post to be deleted.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of post deletion.
     * @throws AuthorizationException
     */
    public function destroy(Post $post) : JsonResponse
    {
        //Use PostPolicy to authorize can you see post
        $this->authorize('delete', $post);

        // Delete the specified post using the PostService
        $this->postService->destroy($post);

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
            'data' => null
        ]);
    }
}

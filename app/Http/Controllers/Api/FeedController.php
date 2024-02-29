<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Services\FeedService;
use Illuminate\Http\JsonResponse;

class FeedController extends Controller
{
    /**
     * FeedController constructor.
     *
     * @param \App\Services\FeedService $feedService The feed service.
     */
    public function __construct(protected FeedService $feedService)
    {
    }

    /**
     * Retrieve and display posts from the user's friends.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing posts from friends.
     */
    public function feed() : JsonResponse
    {
        // Retrieve the feed from the FeedService
        $feed = $this->feedService->feed();

        // Return a JSON response with posts from friends and pagination information
        return response()->json([
            'success' => true,
            'message' => 'Posts from my friends',
            'data' => [
                'posts' => PostResource::collection($feed),
                'pagination' => [
                    'current_page' => $feed->currentPage(),
                    'last_page' => $feed->lastPage(),
                    'per_page' => $feed->perPage(),
                    'total' => $feed->total(),
                ]
            ]
        ]);
    }
}

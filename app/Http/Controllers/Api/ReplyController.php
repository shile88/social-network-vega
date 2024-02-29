<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use App\Services\ReplyService;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function __construct(protected ReplyService $replyService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Post $post,Comment $comment)
    {
        $replies = $this->replyService->index($comment);

        if(!$replies)
            return response()->json([
                'success' => true,
                'message' => 'No replies',
                'data' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All replies',
                'data' => [
                    'replies' => ReplyResource::collection($replies),
                    'pagination' => [
                        'current_page' => $replies->currentPage(),
                        'last_page' => $replies->lastPage(),
                        'per_page' => $replies->perPage(),
                        'total' => $replies->total(),
                    ]
                ]
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReplyRequest $request, Post $post, Comment $comment)
    {
        $replay = $this->replyService->store($request, $post, $comment);

        return response()->json([
            'success' => true,
            'message' => 'New reply created',
            'data' => [
                'reply' => ReplyResource::make($replay)
            ]
        ]);
    }

}

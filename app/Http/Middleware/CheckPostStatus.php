<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckPostStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //Get post if from route
        $postId = $request->route('post')->id;
        $post = Post::query()->findOrFail($postId);

        // Check if the post is not accepted in reports and gives acces to that post
        if ($post->reports()->where('status', 'accepted')->exists()) {
            throw new AccessDeniedHttpException('Access denied. This post has been reported and is not accessible.');
        }

        return $next($request);
    }
}

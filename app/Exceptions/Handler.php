<?php

namespace App\Exceptions;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use ErrorException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
       $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
           if ($request->is('api/*')) {
               return response()->json([
                   'success' => false,
                   'message' => $e->getMessage(),
               ], 403);
           }
       });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

    }

    public function render($request, \Throwable $e)
    {
        if($e instanceof ModelNotFoundException) {
            $class = match($e->getModel()) {
                User::class => 'User',
                Post::class => 'Post',
                Comment::class => 'Comment',
                Like::class => 'Like',
                Report::class => 'Report',
                default => 'Record'
            };
            return response()->json([
                'success' => false,
                'message'=> $class . ' not found'
            ], 404);
        }
        return parent::render($request, $e);
    }
}

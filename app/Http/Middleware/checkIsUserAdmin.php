<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkIsUserAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        //Checks if user is admin and gives permission
        if(!auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You dont have permission for this page.'
            ],403);
        }

        return $next($request);
    }
}

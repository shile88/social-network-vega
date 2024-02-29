<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\api\ReplyController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/{email}/reset-password', [UserController::class, 'resetPassword']);
Route::post('/{token}/reset-password', [UserController::class, 'confirmReset']);

//Protected routes
Route::middleware('auth:sanctum')->group(function() {
    //Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    //Route for users connection and search
    Route::get('/users', [UserController::class, 'search']);
    Route::get('/users/my-friends', [UserController::class, 'myFriends']);
    Route::post('/users/{user}/send-connection', [UserController::class, 'sendConnection']);
    Route::get('/users/received-connections', [UserController::class, 'receivedConnections']);
    Route::patch('/users/accept-connection/{user}', [UserController::class, 'acceptConnection']);
    Route::patch('/users/decline-connection/{user}', [UserController::class, 'declineConnection']);

    Route::middleware(['checkPostStatus'])->group(function () {
        //Routes for posts
        Route::apiResource('/posts', PostController::class);

        //Routes for comments
        Route::apiResource('/posts/{post}/comments', CommentController::class);

        //Routes for replies
        Route::get('/posts/{post}/comments/{comment}/replies', [ReplyController::class, 'index']);
        Route::post('/posts/{post}/comments/{comment}/replies', [ReplyController::class, 'store']);

        //Routes for likes
        Route::apiResource('/posts/{post}/likes', LikeController::class);
    });

    //Routes for feed
    Route::get('/feed', [FeedController::class, 'feed']);
    Route::get('/feed/{feed}', [FeedController::class, 'show']);

    //Routes for reports
    Route::apiResource('/reports', ReportController::class)
        ->middleware('checkIsUserAdmin')
        ->except(['storeUser','storePost']);
    Route::post('/users/{user}/reports/create', [ReportController::class, 'storeUser']);
    Route::post('/posts/{post}/reports/create', [ReportController::class, 'storePost']);

    //Route for exporting files
    Route::get('/export', [ExportController::class, 'export']);
});







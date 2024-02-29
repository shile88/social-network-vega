<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResetPasswordRequest;
use App\Http\Resources\ConnectionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    /**
     * UserController constructor.
     *
     * @param \App\Services\UserService $userService The user service.
     */
    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Search for users based on the given criteria.
     *
     * @param \Illuminate\Http\Request $request The request containing search criteria.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the list of users.
     */
    public function search(Request $request) : JsonResponse
    {
        // Retrieve users based on the search criteria using the UserService
        $users = $this->userService->search($request);

        // Check if any users were found
        return $users->isEmpty() ?
                response()->json([
                    'success' => false,
                    'message' => 'Didn\'t find any users with the given criteria',
                    'data' => null
                ], ResponseAlias::HTTP_NOT_FOUND)
            :
                response()->json([
                    'success' => true,
                    'message' => 'Here is a list of users',
                    'data' => [
                        'users' => UserResource::collection($users),
                    ]
                ]);
    }

    public function myFriends()
    {
        $connections = $this->userService->myFriends();

        return response()->json([
            'success' => true,
            'message' => 'All my connections',
            'data' => [
                'connections' => ConnectionResource::collection($connections)
            ]
        ]);
    }

    /**
     * Send a connection request to another user.
     *
     * @param \App\Models\User $user The user to send a connection request to.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the connection request.
     */
    public function sendConnection(User $user) : JsonResponse
    {
        // Attempt to send a connection request using the UserService
        $connection = $this->userService->sendConnection($user);

        // Check the result of the connection request
        if($user->id == auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cant send connection to yourself',
                'data' => null
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        } elseif ($connection === null) {
            return response()->json([
                'success' => false,
                'message' => 'Connection already exists',
                'data' => null
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Connection sent',
                'data' => [
                    'connection' => $connection,
                ]
            ], ResponseAlias::HTTP_CREATED);
        }
    }


    public function receivedConnections()
    {
        $connections = $this->userService->receivedConnections();

        return response()->json([
            'success' => true,
            'message' => 'List of your received connection',
            'data' => [
                'connection' => ConnectionResource::collection($connections)
            ]
        ]);
    }

    /**
     * Get a list of received connection requests.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the list of received connections.
     */
    public function acceptConnection(User $user) : JsonResponse
    {
        // Retrieve received connections using the UserService
        $connection = $this->userService->acceptConnection($user);

        // Check the result of accepting the connection request
        return $connection ?
                response()->json([
                    'success' => true,
                    'message' => 'Connection accepted successfully',
                    'data' => [
                        'connection' => ConnectionResource::make($connection),
                    ]
                ])
            :
                response()->json([
                    'success' => false,
                    'message' => 'Connection not found or already accepted',
                    'data' => null
                ], ResponseAlias::HTTP_NOT_FOUND);
    }

    /**
     * Decline a connection request from another user.
     *
     * @param \App\Models\User $user The user whose connection request is being declined.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of declining the connection.
     */
    public function declineConnection(User $user) : JsonResponse
    {
        // Attempt to decline a connection request using the UserService
        $connection = $this->userService->declineConnection($user);

        // Check the result of declining the connection request
        return $connection ?
                response()->json([
                    'success' => true,
                    'message' => 'Connection declined',
                    'data' => [
                        'connection' => ConnectionResource::make($connection),
                    ]
                ])
           :
                response()->json([
                    'success' => false,
                    'message' => 'Connection not found or already declined',
                    'data' => null
                ], ResponseAlias::HTTP_NOT_FOUND);
    }

    public function resetPassword($email)
    {
        $resetPassword = $this->userService->resetPassword($email);

        if(!$resetPassword) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email',
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reset email sent',
            'data' => null
        ]);
    }

    public function confirmReset($token, StoreResetPasswordRequest $request)
    {
        $reset = $this->userService->confirmReset($token, $request);

        if(!$reset)
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     *
     * @param \App\Services\AuthService $authService The authentication service.
     */
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Register a new user.
     *
     * @param \App\Http\Requests\RegisterRequest $request The register request containing user data.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the registration.
     */
    public function register(RegisterRequest $request) : JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User was created.',
            'data' => [
                'user' => UserResource::make($user),
//                'token' => [
//                    'access_token' => $token,
//                    'token_type' => 'Bearer',
//                ]
            ],
        ], ResponseAlias::HTTP_CREATED);
    }

    /**
     * Log in a user.
     *
     * @param \App\Http\Requests\LoginRequest $request The login request containing user credentials.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the login.
     */
    public function login(LoginRequest $request) : JsonResponse
    {
        $credentials = $request->validated();

        $result = $this->authService->login($credentials);

        //Check for result and provide correct response
        if(!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Provided email address or password is incorrect',
                'data' => null
            ],
                ResponseAlias::HTTP_UNAUTHORIZED);
        }

        [$user, $token] = $result;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => UserResource::make($user),
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]
        ]);
    }

    /**
     * Log out the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the logout.
     */
    public function logout() : JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'success' => true,
            'message' => 'Log out successful',
            'data' => null
        ]);
    }
}

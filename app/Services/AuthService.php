<?php

namespace App\Services;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $userData
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function register($userData)
    {
        //Create and return user and token
        return User::query()->create($userData);
    }

    /**
     * Attempt to log in with given credentials.
     *
     * @param array $credentials
     * @return array|bool
     */
    public function login($credentials)
    {
        //Attempt to log in with given credentials
        if(!auth()->attempt($credentials)) {
            return false;
        }

        //Log in was successful
        $user = auth()->user();

        // Check if the user is banned
        if ($user->status === 'banned') {
            auth()->logout();
            return false;
        }

        // Generate and return user token
        $token = $user->createToken('access_token')->plainTextToken;

        return [$user, $token];
    }

    /**
     * Log out the authenticated user by revoking their tokens.
     *
     * @return void
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
    }
}

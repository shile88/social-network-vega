<?php

namespace App\Services;

use App\Models\Connection;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Search for users based on the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($request)
    {
        //Uses scope to search for user
        return User::query()
            ->search($request->search)
            ->get();
    }

    /**
     * Get the authenticated user's friends.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function myFriends()
    {
        //Uses relation to get all friends of user with status accepted
        return auth()->user()->friends();
    }

    /**
     * Send a connection request to the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function sendConnection($user)
    {
        //Uses relation to get all friends id's independent to status
        $myFriendsIDs = auth()->user()?->allConnections()->pluck('pivot')->toArray();

        //Loop and checks is there already connection with same id's
        foreach ($myFriendsIDs as $myFriend) {
            if (($myFriend['user_id'] == $user->id) || ($myFriend['friend_id'] == $user->id)) {
                return null;
            }
        }

        return Connection::query()->create([
            'user_id' => auth()->id(),
            'friend_id' => $user->id,
            'status' => 'pending'
        ]);
    }

    /**
     * Get received connection requests for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function receivedConnections()
    {
        //Uses relation to check connection request user received
        return auth()->user()->receivedConnections;
    }

    /**
     * Accept a connection request from the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function acceptConnection($user)
    {
        //Checks existing connection
        $connection = Connection::query()->where('user_id', $user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        //Updates connection to accepted
        if($connection) {
            $connection->update(['status' => 'accepted']);

            return $connection;
        } else {
            return false;
        }
    }

    /**
     * Decline a connection request from the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\Connection|bool
     */
    public function declineConnection($user)
    {
        //Checks existing connection
        $connection = Connection::query()->where('user_id', $user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        //Updates connection to denied
        if($connection) {
            $connection->update(['status' => 'denied']);

            return $connection;
        } else {
            return false;
        }
    }

    /**
     * Initiate the password reset process by sending an email with a reset link.
     *
     * @param  string  $email
     * @return bool
     */
    public function resetPassword($email)
    {
        //Finds users with email from request
        $userWithEmail = User::query()->where('email', $email)->first();

        if(!$userWithEmail) {
            return false;
        }

        //Create token and adds it to table
        $token = base64_encode($email);
        DB::table('password_reset_tokens')->updateOrInsert([
                'email' => $email
            ],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ],
        );

        //Notifies users with email for reset password procedure
        $userWithEmail->notify(new ResetPasswordNotification($token));

        return true;
    }

    /**
     * Confirm the password reset using the provided token and new password.
     *
     * @param  string  $token
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function confirmReset($token, $request)
    {
        //Checks for user with token from request
        $userWithToken = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if(!$userWithToken) {
            return false;
        }

        //Checks and update user with new password
        User::query()->where('email', $userWithToken->email)
            ->update(['password' => Hash::make($request->validated()['password'])]);

        //Deletes token from table
        DB::table('password_reset_tokens')->where('token', $token)->delete();

        return true;
    }
}

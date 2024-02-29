<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Forget user-related caches.
     *
     * @param mixed $model
     * @return void
     */
    public function forgetUserCaches($model = null)
    {
        //Retrieve the authenticated user's friend IDs.
        $myFriendsIDs = auth()->user()?->friends()->pluck('pivot')->toArray();

        // Iterate through user's friends and clear cache if the model user is a friend
        if($model)
                foreach ($myFriendsIDs as $myFriend) {
                    if (($myFriend['user_id'] == $model->user->id) || ($myFriend['friend_id'] == $model->user->id)) {
                        Cache::forget(auth()->id() . '_feed');
                        break;
                    }
                }

        // Forget the general posts cache
        Cache::forget(auth()->id() . '_posts');
    }
}

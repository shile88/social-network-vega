<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class FeedService
{
    /**
     * Retrieve and cache the user's feed.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function feed()
    {
        //Use caching to remember the user's feed based on friends' posts.
        return Cache::rememberForever(auth()->id() . '_feed', function () {
            $friendsIDs = User::query()
                ->where('id', auth()->id())
                ->with('friendsOfMine','friendOf')
                ->first()
                ->friends()->pluck('id');

            // Retrieve and paginate posts from the user's friends
            return Post::query()->whereIn('user_id', $friendsIDs)
                ->orderBy('created_at', 'desc')
                ->with('likes', 'comments')
                ->paginate(10);
        });
    }
}

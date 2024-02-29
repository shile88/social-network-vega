<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    public function __construct(protected CacheService $cacheService)
    {
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(): void
    {
       $this->cacheService->forgetUserCaches();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->cacheService->forgetUserCaches($post);
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->cacheService->forgetUserCaches($post);
    }

}

<?php

namespace App\Observers;

use App\Models\Like;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class LikeObserver
{
    public function __construct(protected CacheService $cacheService)
    {
    }

    public function created(): void
    {
        $this->cacheService->forgetUserCaches();
    }


    public function updated(Like $like): void
    {
        $this->cacheService->forgetUserCaches($like);
    }


    public function deleted(Like $like): void
    {
        $this->cacheService->forgetUserCaches($like);
    }
}

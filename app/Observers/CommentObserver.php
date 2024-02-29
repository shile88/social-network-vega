<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{
    public function __construct(protected CacheService $cacheService)
    {
    }

    public function created(): void
    {
        $this->cacheService->forgetUserCaches();
    }


    public function updated(Comment $comment): void
    {
        $this->cacheService->forgetUserCaches($comment);
    }


    public function deleted(Comment $comment): void
    {
        $this->cacheService->forgetUserCaches($comment);
    }
}

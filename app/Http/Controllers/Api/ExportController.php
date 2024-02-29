<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Services\CacheService;
use App\Services\ExportCsvService;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * ExportController constructor.
     *
     * @param \App\Services\ExportCsvService $exportCsvService The CSV export service.
     * @param \App\Services\CacheService $cacheService The cache service.
     */
    public function __construct(protected ExportCsvService $exportCsvService, protected CacheService $cacheService)
    {
    }

    public function export()
   {
       // Retrieve the posts of the authenticated user
       $posts = auth()->user()->posts;
       $comments = auth()->user()->comments;
       $likes = auth()->user()->likes;

       // Use the ExportCsvService to export multiple CSV files
       return $this->exportCsvService->exportMultipleFiles($posts, $comments, $likes);
   }
}

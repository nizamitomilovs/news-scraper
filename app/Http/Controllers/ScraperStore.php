<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Crawler\CrawlerStoreService;
use Illuminate\Http\JsonResponse;

class ScraperStore extends Controller
{
    private CrawlerStoreService $service;

    public function __construct(CrawlerStoreService $service)
    {
        $this->service = $service;
    }

    public function __invoke(): JsonResponse
    {
        $scrapedNews = $this->service->execute();

        return response()->json([
            'news' => $scrapedNews
        ]);
    }
}

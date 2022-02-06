<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Crawler\CrawlerIndexService;
use Illuminate\Http\JsonResponse;

class ScraperIndex
{
    private CrawlerIndexService $service;

    public function __construct(CrawlerIndexService $service)
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

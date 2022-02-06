<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Scraper\ScraperIndexService;
use Illuminate\Http\JsonResponse;

class ScraperIndex
{
    private ScraperIndexService $service;

    public function __construct(ScraperIndexService $service)
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Scraper\ScraperStoreService;
use Illuminate\Http\JsonResponse;

class ScraperStore extends Controller
{
    private ScraperStoreService $service;

    public function __construct(ScraperStoreService $service)
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

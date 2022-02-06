<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Crawler\CrawlerServiceInterface;
use Illuminate\Http\JsonResponse;

class ScraperController extends Controller
{
    private CrawlerServiceInterface $service;
    public function __construct(CrawlerServiceInterface $service)
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

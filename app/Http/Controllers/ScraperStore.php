<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Scraper\ScraperStoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScraperStore extends Controller
{
    private ScraperStoreService $service;

    public function __construct(ScraperStoreService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $page = $this->validateRequest($request);

        $scrapedNews = $this->service->execute($page);

        return response()->json([
            'news' => $scrapedNews
        ]);
    }

    protected function validateRequest(Request $request): int
    {
        $validator = Validator::make($request->all(), [
            'page' => ['integer', 'max:20', 'min:1'],
        ]);

        $payload = $validator->validate();

        return (int) $payload['page'] ?? 1;
    }
}

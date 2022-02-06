<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Scraper\ScraperUpdatePostService;
use Illuminate\Http\JsonResponse;

class ScraperUpdatePost
{
    private ScraperUpdatePostService  $service;
    public function __construct(ScraperUpdatePostService  $service)
    {
        $this->service = $service;
    }

    public function __invoke(string $id): JsonResponse
    {
        $post = $this->service->execute($id);

        return response()->json([
            'post' => $post
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Scraper\ScraperUpdatePostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScraperUpdatePost
{
    private ScraperUpdatePostService $service;

    public function __construct(ScraperUpdatePostService $service)
    {
        $this->service = $service;
    }

    public function __invoke(string $id): JsonResponse
    {
        try {
            $id = $this->validateRequest($id);

            $post = $this->service->execute($id);
        } catch (ValidationException | NotFoundHttpException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json([
            'post' => $post
        ]);
    }

    /**
     * @throws ValidationException
     */
    protected function validateRequest(string $id): string
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['string', 'required', 'max:30'],
        ]);

        $payload = $validator->validate();

        return $payload['id'];
    }
}

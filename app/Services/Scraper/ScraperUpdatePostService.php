<?php

declare(strict_types=1);

namespace App\Services\Scraper;

use App\Models\Post;
use App\Repository\NewsRepositoryInterface;
use Goutte\Client;

class ScraperUpdatePostService
{
    public const ENDPOINT_KEY = 'item?id=';

    private Client $crawlerClient;
    private NewsRepositoryInterface $newsRepository;
    private string $webUrl;

    public function __construct(
        string                  $webUrl,
        Client                  $crawlerClient,
        NewsRepositoryInterface $newsRepository
    )
    {
        $this->crawlerClient = $crawlerClient;
        $this->webUrl = $webUrl;
        $this->newsRepository = $newsRepository;
    }

    public function execute(string $postId): Post
    {
        $this->newsRepository->getPost($postId);

        $crawler = $this->crawlerClient->request('GET', $this->webUrl . self::ENDPOINT_KEY . $postId);

        $points = $crawler->filter('.score')->each(function ($node) {
            return explode(' ', $node->text())[0];
        });

        var_dump($points);

        return $this->newsRepository->updatePost($postId, (int) $points);
    }
}

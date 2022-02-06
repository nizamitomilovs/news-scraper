<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use App\Repository\NewsRepositoryInterface;

class CrawlerIndexService
{
    private NewsRepositoryInterface $newsRepository;

    public function __construct(NewsRepositoryInterface $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function execute()
    {
        return $this->newsRepository->getNews();
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Scraper;

use App\Repository\NewsRepositoryInterface;

class ScraperIndexService
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

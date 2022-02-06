<?php

declare(strict_types=1);

namespace App\Repository;

interface NewsRepositoryInterface
{
    /**
     * @param array<string, string> $news
     */
    public function saveNews(array $news): void;
}

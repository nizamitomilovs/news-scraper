<?php

declare(strict_types=1);

namespace App\Repository;

use Iterator;

interface NewsRepositoryInterface
{
    /**
     * @param array<string, string> $news
     */
    public function saveNews(array $news): Iterator;

    public function getNews(): array;
}

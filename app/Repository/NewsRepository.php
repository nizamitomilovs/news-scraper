<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Post;

class NewsRepository implements NewsRepositoryInterface
{
    public function saveNews(array $news): void
    {
        Post::insert($news);
    }

    public function getNews(): array
    {
        return Post::all()->toArray();
    }
}

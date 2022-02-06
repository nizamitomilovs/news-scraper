<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Post;
use Iterator;

interface NewsRepositoryInterface
{
    public function saveNews(array $news): Iterator;

    public function getNews(): array;

    public function getPost(string $postId): Post;

    public function updatePost(string $postId, int $points): Post;
}

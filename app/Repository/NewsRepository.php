<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Post;
use Iterator;

class NewsRepository implements NewsRepositoryInterface
{
    public function saveNews(array $news): Iterator
    {
        foreach ($news as $post) {
            yield Post::updateOrCreate(
                [
                    'id' => $post['id']
                ],
                [
                    'title' => $post['title'],
                    'link' => $post['link'],
                    'points' => (int) $post['points'],
                    'posted_at' => date_create($post['posted_at'])->format('Y-m-d')
                ]
            );
        }
    }

    public function getNews(): array
    {
        return Post::all()->toArray();
    }
}

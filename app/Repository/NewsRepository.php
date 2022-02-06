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
                    'points' => (int)$post['points'],
                    'posted_at' => date_create($post['posted_at'])->format('Y-m-d')
                ]
            );
        }
    }

    public function getNews(): array
    {
        return Post::all()->toArray();
    }

    public function getPost(string $postId): Post
    {
        return Post::where('id', $postId)->first();
    }

    public function updatePost(string $postId, int $points): Post
    {
        $postModel = Post::where('id', $postId)->first();
        $postModel->update([
            'points' => $points,
        ]);

        $postModel->refresh();

        return $postModel;
    }
}

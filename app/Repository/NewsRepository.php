<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Iterator;
use PDOException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsRepository implements NewsRepositoryInterface
{
    public function saveNews(array $news): Iterator
    {
        foreach ($news as $post) {
            try {
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
            } catch (PDOException $e) {
                throw new InvalidArgumentException('Didnt\t update post with id: ' . $post);
            }
        }
    }

    public function getNews(): array
    {
        return Post::all()->toArray();
    }

    public function getPost(string $postId): Post
    {
        try {
            return Post::where('id', $postId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Post not found: ' . $postId);
        }
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

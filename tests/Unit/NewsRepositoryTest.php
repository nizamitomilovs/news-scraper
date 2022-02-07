<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Post;
use App\Repository\NewsRepository;
use Database\Factories\PostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class NewsRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetsNews(): void
    {
        PostFactory::new()->count(5)->create();

        $repository = new NewsRepository();

        $news = $repository->getNews();
        $this->assertCount(5, $news);
        $this->assertIsArray($news);
    }

    public function testGetPost(): void
    {
        PostFactory::new()->create(['id' => 123]);

        $repository = new NewsRepository();
        $post = $repository->getPost('123');
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(123, $post->id);
    }

    public function testPostNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Post not found: 123');

        $repository = new NewsRepository();
        $repository->getPost('123');
    }

    public function testUpdatePost(): void
    {
        PostFactory::new()->create(['id' => 123, 'points' => 10]);

        $repository = new NewsRepository();
        $post = $repository->updatePost('123', 30);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(30, $post->points);

        $this->assertDatabaseHas('news', [
            'id' => 123,
            'points' => 30
        ]);
    }
}

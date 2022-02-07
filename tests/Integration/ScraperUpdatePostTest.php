<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Post;
use App\Repository\NewsRepositoryInterface;
use Database\Factories\PostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ScraperUpdatePostTest extends TestCase
{
    use DatabaseMigrations;

    public function testWhenPostNotInDatabase(): void
    {
        $response = $this->patch('/scrape/post/123123');

        $response->assertStatus(404)->assertJson([
            'message' => 'Post not found: 123123',
        ]);
    }

    public function testFoundId(): void
    {
        $post = PostFactory::new()->create([
            'id' => 123123,
            'points' => 20
        ]);

        $this->app->instance(NewsRepositoryInterface::class,
            Mockery::mock(NewsRepositoryInterface::class, function (MockInterface $mock){
                $mock->shouldReceive('getPost')
                    ->once()
                    ->with(123123);

                $mock->shouldReceive('updatePost')
                    ->once()
                    ->with(123123, 0)
                    ->andReturn(new Post([
                        'id' => 123123,
                        'points' => 0
                    ]));
            })
        );

        $response = $this->patch('/scrape/post/123123');

        $response->assertStatus(200)
            ->assertJson([
                'post' => [
                    'id' => 123123,
                    'points' => 0
                ]
            ]);
    }
}

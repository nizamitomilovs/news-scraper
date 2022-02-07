<?php

declare(strict_types=1);

namespace Tests\Integration;

use Database\Factories\PostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
        PostFactory::new()->create([
            'id' => 123123
        ]);

        $response = $this->patch('/scrape/post/123123');

        $response->assertStatus(200)
            ->assertJson([
                'post' => [
                    'id' => 123123
                ]
            ]);
    }
}

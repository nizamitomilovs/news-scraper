<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\NewsRepositoryInterface;
use Database\Factories\PostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ScraperStoreTest extends TestCase
{
    use DatabaseMigrations;

    public function testWhenPageIsInvalid(): void
    {
        $response = $this->post('/scrape', [
            'page' => 'definitely-not-valid'
        ]);

        $response->assertStatus(422)
        ->assertJson([
            'status'  => 'The given data was invalid.',
            'message' => ['The page must be an integer.'],
        ]);
    }

    public function testReturnsNews(): void
    {
        $posts = PostFactory::new()->count(3)->create();
        $this->app->instance(NewsRepositoryInterface::class,
            Mockery::mock(NewsRepositoryInterface::class, function (MockInterface $mock) use ($posts) {
                $mock->shouldReceive('saveNews')
                    ->once()
                    ->andYield($posts->toArray());
            })
        );


        $response = $this->post('/scrape')
        ->assertStatus(200);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('news', $data);
        $this->assertGreaterThan(0, count($data['news']));
    }
}

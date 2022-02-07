<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\NewsRepositoryInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ScraperIndexTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetListWhenNoNewsStored(): void
    {
        $response = $this->get('/scrape');

        $response->assertStatus(200)
            ->assertJson([
                'news' => []]);
    }

    public function testReturnsNews(): void
    {
        $this->app[NewsRepositoryInterface::class] = Mockery::mock(NewsRepositoryInterface::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('getNews')
                    ->once()
                    ->andReturn([
                            'title' => 'test',
                            'link' => 'http://url.test',
                            'points' => 20,
                        ]
                    );
            });

        $this->get('/scrape')
            ->assertStatus(200)
            ->assertJson([
                'news' => [
                    'title' => 'test',
                    'link' => 'http://url.test',
                    'points' => 20,
                ]
            ]);
    }
}

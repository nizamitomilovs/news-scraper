<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Post;
use App\Repository\NewsRepositoryInterface;
use App\Services\Scraper\ScraperIndexService;
use App\Services\Scraper\ScraperUpdatePostService;
use Database\Factories\PostFactory;
use Goutte\Client;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class ScrapeServicesTest extends TestCase
{
    use DatabaseMigrations;

    public function testScraperIndexServiceReturnsNews(): void
    {
        $post = PostFactory::new()->create();

        $repository = Mockery::mock(NewsRepositoryInterface::class,
            function (MockInterface $mock) use ($post) {
                $mock->shouldReceive('getNews')
                    ->once()
                    ->andReturn($post->toArray());
            });


        $indexService = new ScraperIndexService($repository);
        $news = $indexService->execute();

        $this->assertArrayHasKey('title', $news);
    }

    public function testScraperUpdatePostService(): void
    {
        $post = PostFactory::new()->create(['id' => 123]);

        $repository = Mockery::mock(NewsRepositoryInterface::class,
            function (MockInterface $mock) use ($post) {
                $mock->shouldReceive('getPost')
                    ->once()
                    ->with('123')
                    ->andReturn($post);

                $mock->shouldReceive('updatePost')
                    ->once()
                    ->with('123', 0)
                    ->andReturn(new Post());
            });

        $client = Mockery::mock(Client::class,
            function (MockInterface $mock) {
            $mock->shouldReceive('request')
                ->once()
                ->andReturn(
                    Mockery::mock(Crawler::class,
                        function (MockInterface $mock) {
                            $mock->shouldReceive('filter')
                                ->once();

                            $mock->shouldReceive('each')
                                ->once();
                        })
                );
        });

        $indexService = new ScraperUpdatePostService('http://test.com', $client, $repository);

        $post = $indexService->execute('123');

        $this->assertDatabaseHas('news', ['id' => '123', 'points' => 0]);
    }
}

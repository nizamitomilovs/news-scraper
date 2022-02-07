<?php

declare(strict_types=1);

namespace Tests\Integration;

use Database\Factories\PostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    use DatabaseMigrations;

    public function testFetchDataCommand(): void
    {
        $this->artisan('scrape-fetch:news')
            ->expectsOutput('Scraping website on page: 1')
            ->expectsOutput('Scraping was successful, please visit website!');

        $this->assertDatabaseCount('news', 30);
    }

    public function testSecondPageFetchDataCommand(): void
    {
        $this->artisan('scrape-fetch:news 2')
            ->expectsOutput('Scraping website on page: 2')
            ->expectsOutput('Scraping was successful, please visit website!');

        $this->assertDatabaseCount('news', 30);
    }

    public function testUpdateCommandWhenIdIsNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Post not found: 2');

        $this->artisan('scrape-update-point:post 2');
    }

    public function testUpdateWasSuccessful(): void
    {
        PostFactory::new()->create(['id' => 30232521, 'points' => 0]);

        $this->artisan('scrape-update-point:post 30232521')
            ->expectsOutput('Updating point for post with id: 30232521');

        $this->artisan('scrape-update-point:post 30232521');

        $this->assertDatabaseMissing('news', [
            'id' => 30232521,
            'points' => 0
        ]);
    }
}

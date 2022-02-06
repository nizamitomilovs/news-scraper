<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Scraper\ScraperUpdatePostService;
use Illuminate\Console\Command;

class ScrapeUpdatePostCommand extends Command
{
    protected $signature = 'scrape-update-point:post {post_id}';

    protected $description = 'Command description';

    private ScraperUpdatePostService $service;

    public function __construct(ScraperUpdatePostService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function handle(): int
    {
        $postId = $this->argument('post_id');
        $this->info('Updating point for post with id: ' . $postId);

        $post = $this->service->execute($postId);

        $this->info('Update was successful, current amount of points: ' . $post->points);

        return 1;
    }
}

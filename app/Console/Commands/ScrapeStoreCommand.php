<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Scraper\ScraperStoreService;
use Illuminate\Console\Command;

class ScrapeStoreCommand extends Command
{
    protected $signature = 'scrape-fetch:news {page=1 : Optional parameter to scrape specific page.}';

    protected $description = 'Scrape trough website for news.';

    private ScraperStoreService $service;

    public function __construct(ScraperStoreService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function handle(): int
    {
        $page = (int) $this->argument('page');

        $this->info('Scraping website on page: ' . $page);

        $this->service->execute($page);

        $this->info('Scraping was successful, please visit website!');

        return 1;
    }
}

<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repository\NewsRepository;
use App\Repository\NewsRepositoryInterface;
use App\Services\Scraper\ScraperStoreService;
use App\Services\Scraper\ScraperUpdatePostService;
use Goutte\Client as GoutteClient;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\HttpClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NewsRepositoryInterface::class, NewsRepository::class);

        $this->app->bind(ScraperStoreService::class, function ($app) {
            return new ScraperStoreService(
                env('WEB_URL'),
                new GoutteClient(HttpClient::create(['timeout' => config('goutte.timeout')])),
                $app->make(NewsRepositoryInterface::class)
            );
        });

        $this->app->bind(ScraperUpdatePostService::class, function ($app) {
            return new ScraperUpdatePostService(
                env('WEB_URL'),
                new GoutteClient(HttpClient::create(['timeout' => config('goutte.timeout')])),
                $app->make(NewsRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}

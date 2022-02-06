<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repository\NewsRepository;
use App\Repository\NewsRepositoryInterface;
use App\Services\Crawler\CrawlerService;
use App\Services\Crawler\CrawlerServiceInterface;
use Goutte\Client as GoutteClient;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\HttpClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NewsRepositoryInterface::class, NewsRepository::class);

        $this->app->bind(CrawlerServiceInterface::class, function ($app) {
            return new CrawlerService(
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

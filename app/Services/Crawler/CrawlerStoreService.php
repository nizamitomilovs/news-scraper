<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use App\Repository\NewsRepositoryInterface;
use Goutte\Client;
use InvalidArgumentException;

class CrawlerStoreService
{
    private const METHOD = 'GET';

    private Client $crawlerClient;
    private NewsRepositoryInterface $newsRepository;
    private string $webUrl;

    public function __construct(
        string $webUrl,
        Client $crawlerClient,
        NewsRepositoryInterface $newsRepository
    ){
        $this->crawlerClient = $crawlerClient;
        $this->webUrl = $webUrl;
        $this->newsRepository = $newsRepository;
    }

    public function execute()
    {
        $crawler = $this->crawlerClient->request(self::METHOD, $this->webUrl);

        $scrapedNews = [];

        $crawler->filter('tr.athing')->each(function ($node) use (&$scrapedNews) {
            $newsId = $node->extract(['id'])[0];
            $title = $node->filter('.title')->eq(1)->text();
            $link = $node->filter('.titlelink');
            $link = $link->extract(['href'])[0];

            $scrapedNews[$newsId] = ['id' => $newsId, 'title' => $title, 'link' => $link];
            $scrapedNews[$newsId] = ['id' => $newsId, 'title' => $title, 'link' => $link];
        });

        $crawler->filter('td.subtext')->each(function ($node) use (&$scrapedNews) {
            try {
                $pointsClass = $node->filter('.subtext > .score');
                $points = explode(' ', $pointsClass->text());

                $dateClass = $node->filter('.subtext > .age');
                $date = $dateClass->extract(['title'])[0];

                $id = explode('_', $pointsClass->extract(['id'])[0])[1];

                $scrapedNews[$id]['points'] = (int) $points[0];
                $scrapedNews[$id]['posted_at'] = date_create($date)->format('Y-m-d');
            } catch (InvalidArgumentException $e) {
                $dateClass = $node->filter('.subtext > .age');
                $idClass = $node->filter('.subtext > .age > a');

                $date = $dateClass->extract(['title'])[0];

                $idData = $idClass->extract(['href'])[0];
                $id = explode('=', $idData)[1];

                $scrapedNews[$id]['posted_at'] = date_create($date)->format('Y-m-d');
                $scrapedNews[$id]['points'] = strlen($node->filter('.subtext > .score')->text('')) === 0 ?? 0;
            }
        });

        $scrapedNews = $this->newsRepository->saveNews($scrapedNews);

        return iterator_to_array($scrapedNews);
    }
}

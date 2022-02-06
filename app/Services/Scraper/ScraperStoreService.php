<?php

declare(strict_types=1);

namespace App\Services\Scraper;

use App\Repository\NewsRepositoryInterface;
use Goutte\Client;
use InvalidArgumentException;

class ScraperStoreService
{
    private const METHOD = 'GET';

    private Client $crawlerClient;
    private NewsRepositoryInterface $newsRepository;
    private string $webUrl;

    public function __construct(
        string                  $webUrl,
        Client                  $crawlerClient,
        NewsRepositoryInterface $newsRepository
    )
    {
        $this->crawlerClient = $crawlerClient;
        $this->webUrl = $webUrl;
        $this->newsRepository = $newsRepository;
    }

    public function execute(int $page): array
    {
        $crawler = $this->crawlerClient->request(self::METHOD, $this->webUrl . '?p=' . $page);
        $scrapedNews = [];

        $crawler->filter('tr.athing')->each(function ($node) use (&$scrapedNews) {
            $newsId = implode('', $node->extract(['id']));
            $title = $node->filter('.title')->eq(1)->text();

            $link = $node->filter('.titlelink');
            $link = implode('', $link->extract(['href']));
            $link = $this->validateAndConvert($link);

            $scrapedNews[$newsId] = ['id' => $newsId, 'title' => $title, 'link' => $link];
        });

        $crawler->filter('td.subtext')->each(function ($node) use (&$scrapedNews) {
            $date = implode('', $node->filter('.age')->extract(['title']));

            $idClass = $node->filter('.age > a');
            $id = explode('=', implode('', $idClass->extract(['href'])))[1];

            if (true == array_key_exists($id, $scrapedNews)) {
                $scrapedNews[$id]['posted_at'] = date_create($date)->format('Y-m-d');
                $scrapedNews[$id]['points'] = (int)$node->filter('.score')->text('0');
            }
        });

        $scrapedNews = $this->newsRepository->saveNews($scrapedNews);

        return iterator_to_array($scrapedNews);
    }

    //some links are redirects to comment sections, check and if needed format it
    private function validateAndConvert(string $link): string
    {
        if (str_starts_with($link, ScraperUpdatePostService::ENDPOINT_KEY)) {
            return $this->webUrl . ScraperUpdatePostService::ENDPOINT_KEY;
        }

        return $link;
    }
}

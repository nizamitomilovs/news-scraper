### Hacker news Scraper

Simple SPA which uses laravel 8 and vue.js, to scrape news from <a href="https://news.ycombinator.com/">here</a>.

Application supports php versions 8+

### Setup

1. Clone project to your local envirement.
2. Create .env file from .env.example and configure database credentials
    * cp .env.example .env
3. Execute commands:
   * composer install 
   * npm i && npm run dev
   * docker-compose up -d
   * docker exec -it scraper-app php artisan key:generate
   * docker exec -it scraper-app php artisan migrate


After installation web application should be available from:
http://localhost:8000/

As scraper can be done directly from the website, it can also be done by command
1. Command responsible for fetching all posts from website, where page is optional parameter, if not specified it will pull first page
    * scrape-fetch:news page
2. Command which is responsible for updating points for specific article, where article_id is required parameter
   * scrape-update-point:post article_id

To run test use
>vendor/bin/phpunit

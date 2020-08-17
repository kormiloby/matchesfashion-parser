<?php
namespace Matchesfashion;

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use Clue\React\Mq\Queue;

class Scraper
{
    /**
     * @var Browser
     */
    private $client;

    private $builder;

    /**
     * @var array
     */
    private $scraped = [];

    private $errors = [];

    /**
     * Count items per page
     * @var integer
     */
    protected $noOfRecordsPerPage = 1000;

    public function __construct(Browser $client, $builder)
    {
        $this->client = $client;

        $this->builder = $builder;
    }

    public function scrape(array $urls = [], $headers = [], $concurrencyLimit = 10)
    {

        $queue = new Queue($concurrencyLimit, null, function ($url, $headers) {
            return $this->client->get($url, $headers);
        });

        $this->scraped = [];
        foreach ($urls as $url) {
             // $promise = $queue
             $this->client->get($url)->then(
                function (ResponseInterface $response) {
                   $this->scraped[] = $this->builder->extractFromHtml((string) $response->getBody());
                   $this->echoStatus(count($this->scraped));
                },
                function (Exception $exception) use ($url) {
                   $this->errors[$url] = $exception->getMessage();
                });
        }
    }

    public function echoStatus(int $count)
    {
        system('clear');
        echo "Loaded " . (string) $count . " pages\n";
        flush();
    }

    public function getData()
    {
        return $this->builder->getData($this->scraped);
    }

    public function clearData()
    {
        $this->scraped = [];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

<?php
namespace Matchesfashion\Builder;

use Symfony\Component\DomCrawler\Crawler;
use Matchesfashion\Model\Url;

class CategoryUrlBuilder
{
    protected $selecter = '.filter__box__category a';

    public function extractFromHtml(string $html)
    {
        $urls = [];
        $crawler = new Crawler($html);
        $items = $crawler->filter($this->selecter);
        foreach ($items as $item) {
            $urls[] = $this->build($item->attributes[0]->value);
        }

        return $urls;
    }

    public function build(string $url)
    {
        return new Url($url);
    }

    public function getData(array $data)
    {
        $categoryUrls = [];

        foreach ($data as $array) {
          foreach ($array as $categoryUrl) {
            if ($categoryUrl->url !== '#') {
              $categoryUrls[] = 'https://www.matchesfashion.com' .  $categoryUrl->url;
            }
          }
        }

        return $categoryUrls;
    }
}

<?php
namespace Matchesfashion\Builder;

use Matchesfashion\Model\Item;
use Symfony\Component\DomCrawler\Crawler;

class ItemBuilder
{
    public function extractFromHtml($html)
    {
        $crawler = new Crawler($html);

        //0. Url
        $itemsArray = [];
        $items = $crawler->filter('.lister__item__image a');
        foreach ($items as $item) {
            $itemsArray[] = [
              'url' => $item->attributes[0]->value
            ];
        }

        //1. Photo and externalId
        $tempArray = [];
        $photoItems = $crawler->filter('.lister__item .lister__item__image img');
        foreach ($photoItems as $item) {
            $tempItem = array_shift($itemsArray);
            $tempItem['image'] = $item->attributes[1]->value;
            $tempItem['external_id'] = $item->attributes[2]->value;
            array_push($tempArray, $tempItem);
        }
        $itemsArray = $tempArray;

        //3. Price
        $tempArray = [];
        $items = $crawler->filter('.lister__item__price strike');
        foreach ($items as $item) {
            $tempItem = array_shift($itemsArray);
            $tempItem['price'] = $item->nodeValue;
            array_push($tempArray, $tempItem);
        }
        $itemsArray = $tempArray;

        // 4. Sale
        $tempArray = [];
        $items = $crawler->filter('.lister__item__price-down');
        foreach ($items as $item) {
          $tempItem = array_shift($itemsArray);
          $tempItem['sale'] = trim($item->nodeValue);
          array_push($tempArray, $tempItem);
        }
        $itemsArray = $tempArray;

        //5. Sizes
        $tempArray = [];
        $items = $crawler->filter('.sizes');
        foreach ($items as $item) {
            $sizesNodes = $item->childNodes;
            $sizes = [];
            for ($i = 0; $i < count($sizesNodes); $i++) {
              if (isset($sizesNodes[$i]->attributes[0])) {
                  $sizes[] = $sizesNodes[$i]->nodeValue;
              }
            }
            $tempItem = array_shift($itemsArray);
            $tempItem['size'] = $sizes;
            array_push($tempArray, $tempItem);
        }
        $itemsArray = $tempArray;

        //6. Brand name
        $tempArray = [];
        $items = $crawler->filter('.lister__item__title');
        foreach ($items as $item) {
            $tempItem = array_shift($itemsArray);
            $tempItem['brand'] = $item->nodeValue;
            array_push($tempArray, $tempItem);
        }
        $itemsArray = $tempArray;

        //7. Description
        $tempArray = [];
        $items = $crawler->filter('.lister__item__details');
        foreach ($items as $item) {
            $tempItem = array_shift($itemsArray);
            $tempItem['description'] = $item->nodeValue;
            array_push($tempArray, $tempItem);
        }
        $itemsArray = $tempArray;

        //8. Category
        $tempArray = [];
        $items = $crawler->filter('.breadcrumb a');
        foreach ($items as $item) {
            $tempArray[] = trim($item->nodeValue);
        }

        $objArray = [];
        foreach ($itemsArray as $item) {
            $item['gender'] = $tempArray[0];
            $item['category'] = $tempArray[2];
            $item['sub_category'] = $tempArray[3];
            $itemObj = $this->build($item);
            $objArray[] = $itemObj;
        }

        $resultArray = [
          'objArray' => $objArray,
          'gender' => $tempArray[0],
          'cat' =>  $tempArray[2],
          'subcat' => $tempArray[3]
        ];

        return $resultArray;
    }


    public function build(array $item)
    {
        return new Item($item);
    }

    public function getData(array $data)
    {
        return $data;
    }
}

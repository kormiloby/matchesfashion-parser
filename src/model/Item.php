<?php
namespace Matchesfashion\Model;

class Item
{
    public $url;
    public $external_id;
    public $brand;
    public $images;
    public $description;
    public $gender;
    public $sizes;
    public $isAvailable = true;
    public $initPrice;
    public $salePrice;
    public $category;
    public $subCategiry;

    public function __construct(array $item)
    {
        $this->url = $item['url'];
        $this->external_id = $item['external_id'];
        $this->brand = $item['brand'];
        $this->images['cutOut'] = $item['image'];
        $this->images['model'] = $this->imageToModel();
        $this->description = $item['description'];
        $this->gender = $item['gender'];
        $this->sizes = $item['size'];
        $this->initPrice = (int) $item['price'];
        $this->salePrice = (int) $item['sale'];
        $this->category = $item['category'];
        $this->subCategiry = $item['sub_category'];
    }

    private function imageToModel()
    {
        $imageUlrArray = \explode('/', $this->images['cutOut']);
        $imageNameArray = \explode('_', $imageUlrArray[count($imageUlrArray)-1]);
        $imageNameArray[1] = '2';
        $imageNameString = \implode('_', $imageNameArray);
        $imageUlrArray[count($imageUlrArray)-1] = $imageNameString;
        $imageUrlString = \implode('/', $imageUlrArray);

        return $imageUrlString;
    }
}

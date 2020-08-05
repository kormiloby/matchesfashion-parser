<?php
namespace Matchesfashion\Model;

class Item
{
    public $url;
    public $brand;
    public $cutOut;
    public $model;
    public $description;
    public $gender;
    public $sizes;
    public $isAvailable = true;
    public $price;
    public $sale;
    public $category;
    public $subCategiry;

    public function __construct(array $item)
    {
        $this->url = $item['url'];
        $this->brand = $item['brand'];
        $this->cutOut = $item['image'];
        $this->model = $this->imageToModel();
        $this->description = $item['description'];
        $this->gender = $item['gender'];
        $this->sizes = $item['size'];
        $this->price = $item['price'];
        $this->sale = $item['sale'];
        $this->category = $item['category'];
        $this->subCategiry = $item['sub_category'];
    }



    private function imageToModel()
    {
        $imageUlrArray = \explode('/', $this->cutOut);
        $imageNameArray = \explode('_', $imageUlrArray[count($imageUlrArray)-1]);
        $imageNameArray[1] = '2';
        $imageNameString = \implode('_', $imageNameArray);
        $imageUlrArray[count($imageUlrArray)-1] = $imageNameString;
        $imageUrlString = \implode('/', $imageUlrArray);

        return $imageUrlString;
    }
}

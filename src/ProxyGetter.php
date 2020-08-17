<?php
namespace Matchesfashion;

class ProxyGetter
{
    protected $proxyes = [];

    public function __construct(string $filePath)
    {
        $this->getProxyList($filePath);
    }

    protected function getProxyList(string $filePath)
    {
        $stringData = file_get_contents($filePath, FILE_USE_INCLUDE_PATH);
        $arrayData = explode("\n", $stringData);
        foreach ($arrayData as $item) {
            if (preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3}):[0-9]{2,5}$/", $item)) {
                array_push($this->proxyes, $item);
            }
        }
    }

    public function getProxy()
    {
        return $this->proxyes[rand(0 , count($this->proxyes)-1)];
    }
}

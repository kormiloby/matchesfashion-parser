<?php
namespace Matchesfashion\Printer;

use React\Filesystem\Filesystem;

class JsonFilePrinter
{
    protected $filesystem;

    private $filename = 'temp.json';

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function print(array $data)
    {
        foreach($data as $pageData) {
            $this->setFilename($pageData['gender'], $pageData['cat'], $pageData['subcat']);

            $itemCount = count($pageData['objArray']);
            $dataString = json_encode($pageData['objArray']);

            if ($itemCount > 0) {
                try {
                    file_put_contents('./output/' . $this->filename, $dataString);
                    $this->echoStatus($this->filename, $itemCount);
                } catch (Exception $e) {
                    echo $e->getMessage(), PHP_EOL;
                    echo $e->getTraceAsString(), PHP_EOL;
                }
            }
        }
    }

    public function echoStatus(string $fileName, int $itemCount)
    {
        echo (string)$itemCount . " items was written in  " . $fileName . "\n";
        flush();
    }

    private function setFilename(string $gender, string $category, string $subCategory)
    {
        $this->filename = $gender . '__' . $category . '__' . $subCategory . '.json';
    }
}

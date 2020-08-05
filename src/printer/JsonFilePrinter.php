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

            $dataString = \json_encode($pageData['objArray']);

            $file = $this->filesystem->file('./output/' . $this->filename);
            $file->open('cw')->then(function(\React\Stream\WritableStreamInterface $stream) use ($dataString) {
                $stream->write($dataString);
                $stream->end();
                echo "Data was written\n";
            });
        }
    }

    private function setFilename(string $gender, string $category, string $subCategory)
    {
        $this->filename = $gender . '__' . $category . '__' . $subCategory . '.json';
    }
}

<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileNameGenerator\Simple as SimpleGenerator;
use FileUpload\FileSystem\Mock;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testGenerator()
    {
        $generator = new SimpleGenerator();
        $playground_path = __DIR__ . '/../playground';

        $filesystem = new Mock();
        $resolver = new Simple($playground_path . '/uploaded');

        $server = ['CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321];
        $file = ['tmp_name' => $playground_path . '/real-image.jpg', 'name' => 'real-image.jpg', 'size' => 30321, 'type' => 'image/jpg', 'error' => 0];

        $fileUpload = new FileUpload($file, $server, $generator);
        $fileUpload->setPathResolver($resolver);
        $fileUpload->setFileSystem($filesystem);

        $filename = "picture.jpg";

        $this->assertEquals($generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, 100, $fileUpload), $filename);
    }
}

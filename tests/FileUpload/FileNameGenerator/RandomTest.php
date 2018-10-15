<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Mock;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple;
use PHPUnit\Framework\TestCase;

class RandomTest extends TestCase
{
    public function testGenerator()
    {
        $generator = new Random(32);

        $playground_path = __DIR__ . '/../playground';
        $filename = "picture.jpg";

        $filesystem = new Mock();
        $resolver = new Simple($playground_path . '/uploaded');

        $server = ['CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321];
        $file = ['tmp_name' => $playground_path . '/real-image.jpg', 'name' => 'real-image.jpg', 'size' => 30321, 'type' => 'image/jpg', 'error' => 0];

        $fileUpload = new FileUpload($file, $server, $generator);
        $fileUpload->setPathResolver($resolver);
        $fileUpload->setFileSystem($filesystem);

        $new_name = $generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload);

        $this->assertEquals(32, strrpos($new_name, "."));
        $this->assertEquals(substr($new_name, strrpos($new_name, ".")), ".jpg");
    }
}

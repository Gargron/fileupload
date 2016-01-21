<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Mock;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple;
use FileUpload\FileNameGenerator\Simple as SimpleGenerator;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    protected $filesystem;

    public function setUp()
    {

    }

    public function testGenerator()
    {

        $generator = new SimpleGenerator();
        $playground_path = __DIR__ . '/../playground';

        $filesystem = new Mock();
        $resolver   = new Simple($playground_path . '/uploaded');

        $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
        $file   = array('tmp_name' => $playground_path . '/real-image.jpg', 'name' => 'real-image.jpg', 'size' => 30321, 'type' => 'image/jpg', 'error' => 0);

        $fileUpload = new FileUpload($file , $server , $generator);
        $fileUpload->setPathResolver($resolver);
        $fileUpload->setFileSystem($filesystem);

        $filename = "picture.jpg";

        $this->assertEquals($generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, 100 ,$fileUpload), $filename);

    }

}

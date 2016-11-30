<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Mock;
use FileUpload\FileSystem\Simple;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple as Path;

class MD5Test extends \PHPUnit_Framework_TestCase
{
    protected $filesystem;

    public function setUp()
    {

    }

    public function testGenerator()
    {

        $generator = new MD5();
        $playground_path = __DIR__ . '/../playground';

        $filename = "picture.jpg";
        $new_filename = md5("picture").".jpg";

        $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
        $file   = array('tmp_name' => $playground_path . '/real-image.jpg', 'name' => 'real-image.jpg', 'size' => 30321, 'type' => 'image/jpg', 'error' => 0);

        $fileUpload = new FileUpload($file, $server, $generator);
        $fileUpload->setFileSystem(new Mock());
        $fileUpload->setPathResolver(new Path($playground_path."/uploaded"));

        $this->assertEquals($generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100",$fileUpload), $new_filename);

    }

}

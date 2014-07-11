<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Simple;

class MD5Test extends \PHPUnit_Framework_TestCase {
    protected $filesystem;

    public function setUp() {

    }

    public function testGenerator() {

        $generator = new MD5();

        $filename = "picture.jpg";
        $new_filename = md5("picture").".jpg";

        $this->assertEquals($generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", new \FileUpload\PathResolver\Simple(''), new Simple()), $new_filename);

    }

}
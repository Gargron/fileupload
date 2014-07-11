<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Mock;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple;
use FileUpload\FileNameGenerator\Simple as SimpleGenerator;

class SimpleTest extends \PHPUnit_Framework_TestCase {
    protected $filesystem;

    public function setUp() {

    }

    public function testGenerator() {

        $generator = new SimpleGenerator();
        $playground_path = __DIR__ . '/../playground';

        $filesystem = new Mock();
        $resolver   = new Simple($playground_path . '/uploaded');

        $filename = "picture.jpg";

        $this->assertEquals($generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, 100 ,$resolver, $filesystem), $filename);

    }

}
<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Mock;
use FileUpload\PathResolver\Simple;

class RandomTest extends \PHPUnit_Framework_TestCase {
    protected $filesystem;

    public function setUp() {

    }

    public function testGenerator() {

        $generator = new Random(32);

        $playground_path = __DIR__ . '/../playground';
        $filename = "picture.jpg";

        $filesystem = new Mock();
        $resolver   = new Simple($playground_path . '/uploaded');

        $new_name = $generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $resolver, $filesystem);

        echo($new_name);

        $this->assertEquals(32, strrpos($new_name, "."));
        $this->assertEquals(substr($new_name, strrpos($new_name, ".")), ".jpg");

    }

}

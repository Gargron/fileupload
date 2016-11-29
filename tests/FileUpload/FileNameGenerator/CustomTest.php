<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Mock;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple;

class CustomTest extends \PHPUnit_Framework_TestCase
{

    public function testStringGenerator()
    {
        $customName = "my_file.jpg";

        $generator = new Custom($customName);

        $playground_path = __DIR__ . '/../playground';

        $filename = $customName;
        $new_filename = $customName;

        $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
        $file = array(
            'tmp_name' => $playground_path . '/real-image.jpg',
            'name' => 'real-image.jpg',
            'size' => 30321,
            'type' => 'image/jpg',
            'error' => 0
        );

        $fileUpload = new FileUpload($file, $server, $generator);

        $fileUpload->setFileSystem(new Mock());
        $fileUpload->setPathResolver(new Simple($playground_path . "/uploaded"));

        $this->assertEquals(
            $generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload), $new_filename
        );

    }

    public function testClosureGenerator()
    {
        $customName = function ($source_name, $type, $tmp_name, $index, $content_range, FileUpload $upload) {
            return $index . $type . $source_name . $tmp_name;
        };

        $generator = new Custom($customName);

        $playground_path = __DIR__ . '/../playground';

        $filename = "picture.jpg";

        $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
        $file = array(
            'tmp_name' => $playground_path . '/real-image.jpg',
            'name' => 'real-image.jpg',
            'size' => 30321,
            'type' => 'image/jpg',
            'error' => 0
        );

        $fileUpload = new FileUpload($file, $server, $generator);

        $fileUpload->setFileSystem(new Mock());
        $fileUpload->setPathResolver(new Simple($playground_path . "/uploaded"));

        $new_filename = $customName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload);

        $this->assertEquals(
            $generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload), $new_filename
        );
    }

    public function testCallableGenerator()
    {

        function generateName() {
            return func_get_arg(0);
        }

        $generator = new Custom("FileUpload\\FileNameGenerator\\generateName");

        $playground_path = __DIR__ . '/../playground';

        $filename = "picture.jpg";

        $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
        $file = array(
            'tmp_name' => $playground_path . '/real-image.jpg',
            'name' => 'real-image.jpg',
            'size' => 30321,
            'type' => 'image/jpg',
            'error' => 0
        );

        $fileUpload = new FileUpload($file, $server, $generator);

        $fileUpload->setFileSystem(new Mock());
        $fileUpload->setPathResolver(new Simple($playground_path . "/uploaded"));

        $new_filename = generateName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload);

        $this->assertEquals(
            $generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload), $new_filename
        );
    }
}

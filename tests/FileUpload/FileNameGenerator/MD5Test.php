<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\Mock;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple as Path;

class MD5Test extends \PHPUnit_Framework_TestCase
{
    protected $filesystem;

    public function setUp()
    {
        $playground_path = __DIR__ . '/../../playground';
        $fixtures_path = __DIR__ . '/../../fixtures';

        if (!is_dir($playground_path)) {
            mkdir($playground_path);
        }

        if (!is_dir($playground_path . '/uploaded')) {
            mkdir($playground_path . '/uploaded');
        }

        copy("$fixtures_path/real-image.jpg", "$playground_path/uploaded/real-image.jpg");
    }

    public function tearDown()
    {
        $file = __DIR__ . '/../../playground/';
        $file .= md5(pathinfo('real-image.jpg', PATHINFO_FILENAME)) . '.jpg';

        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function testGenerator()
    {

        $generator = new MD5();
        $playground_path = __DIR__ . '/../playground';

        $filename = "picture.jpg";
        $new_filename = md5("picture") . ".jpg";

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
        $fileUpload->setPathResolver(new Path($playground_path . "/uploaded"));

        $this->assertEquals($generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload),
            $new_filename);

    }

    public function testUploadFailsBecauseFileAlreadyExistsOnTheFileSystem()
    {
        $generator = new MD5();
        $playground_path = __DIR__ . '/../../playground';

        $filename = "real-image.jpg";

        $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
        $file = array(
            'tmp_name' => $playground_path . '/uploaded/real-image.jpg',
            'name' => 'real-image.jpg',
            'size' => 30321,
            'type' => 'image/jpg',
            'error' => 0
        );

        $fileUpload = new FileUpload($file, $server, $generator);
        $fileUpload->setFileSystem(new Mock());
        $fileUpload->setPathResolver(new Path($playground_path));
        $fileUpload->processAll();

        $this->assertFalse(
            $generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload)
        );
    }

    public function testFileIsUploadedDespiteAlreadyExistingOnTheFileSystem()
    {
        $generator = new MD5(true); //true would override files with the same name
        $playground_path = __DIR__ . '/../../playground';

        $filename = "real-image.jpg";
        $newFileName = md5("real-image") . '.jpg';

        $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
        $file = array(
            'tmp_name' => $playground_path . '/uploaded/real-image.jpg',
            'name' => 'real-image.jpg',
            'size' => 30321,
            'type' => 'image/jpg',
            'error' => 0
        );

        $fileUpload = new FileUpload($file, $server, $generator);
        $fileUpload->setFileSystem(new Mock());
        $fileUpload->setPathResolver(new Path($playground_path));
        $fileUpload->processAll();

        $this->assertEquals(
            $generator->getFileName($filename, "image/jpg", "asdf.jpg", 0, "100", $fileUpload),
            $newFileName
        );
    }
}

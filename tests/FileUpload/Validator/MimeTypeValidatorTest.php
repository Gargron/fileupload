<?php

namespace FileUpload\Validator;

use FileUpload\File;

class MimeTypeValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $directory;

    /**
     * @var MimeTypeValidator
     */
    protected $validator;

    public function testValidMimeType()
    {
        $_FILES['file'] = array(
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        );

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertTrue($this->validator->validate($file, $_FILES['file']['size']));
    }

    public function testInvalidMimeType()
    {
        $_FILES['file'] = array(
            "name" => "fake-image.jpg",
            "tmp_name" => $this->directory . 'fake-image.jpg',
            "size" => 12,
            "error" => 0
        );

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertFalse($this->validator->validate($file, $_FILES['file']['size']));
    }

    protected function setUp()
    {
        $this->directory = __DIR__ . '/../../fixtures/';

        $this->validator = new MimeTypeValidator(array("image/jpeg"));
    }

    public function testSetErrorMessages()
    {
        $_FILES['file'] = array(
            "name" => "fake-image.jpg",
            "tmp_name" => $this->directory . 'fake-image.jpg',
            "size" => 12,
            "error" => 0
        );

        $file = new File($_FILES['file']['tmp_name']);

        $errorMessage = "Invalid file type";

        $this->validator->setErrorMessages(array(
            0 => $errorMessage
        ));

        $this->validator->validate($file, $_FILES['file']['size']);

        $this->assertEquals($errorMessage, $file->error);
    }
}

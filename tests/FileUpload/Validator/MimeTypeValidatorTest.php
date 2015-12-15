<?php

namespace FileUpload\Validator;


use FileUpload\File;

class MimeTypeValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $directory;
    protected $validator;
    protected $file;

    protected function setUp()
    {
        $this->directory = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

        $this->validator = new MimeTypeValidator(["image/jpeg"]);
        $this->file = new File();
    }

    public function testValidMimeType()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $this->file->type = "image/jpeg";

        $this->assertTrue($this->validator->validate($this->file, $_FILES['file']['size']));
    }


    public function testInvalidMimeType()
    {
        $_FILES['file'] = [
            "name" => "fake-image.jpg",
            "tmp_name" => $this->directory . 'fake-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $this->file->type = "text/plain" ;

        $this->assertFalse($this->validator->validate($this->file , $_FILES['file']['size']));
    }


    public function testSetErrorMessages()
    {
        $_FILES['file'] = [
            "name" => "fake-image.jpg",
            "tmp_name" => $this->directory . 'fake-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $this->file->type = "text/plain" ;

        $errorMessage = "Invalid file type" ;

        $this->validator->setErrorMessages([
            0 => $errorMessage
        ]);

        $this->validator->validate($this->file , $_FILES['file']['size']);

        $this->assertEquals($errorMessage , $this->file->error);
    }
}

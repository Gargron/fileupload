<?php

namespace FileUpload\Validator;

use FileUpload\FileUpload;
use FileUpload\File;
use PHPUnit\Framework\TestCase;
use FileUpload\Validator\MimeTypeValidator;

class MimeTypeValidatorTest extends TestCase
{
    protected $directory;

    /**
     * @var MimeTypeValidator
     */
    protected $validator;

    protected function setUp()
    {
        $this->directory = __DIR__ . '/../../fixtures/';
        $this->validator = new MimeTypeValidator([ "image/jpeg"]);
    }

    public function testValidMimeType()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);
        $upload = new FileUpload($_FILES["file"], $_SERVER);

        $this->assertTrue($this->validator->validate($upload, $file, $_FILES['file']['size']));

	$this->assertEquals([], $upload->getErrors(), "Expected the error count to equal 0 since the validation was successful");
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
        $upload = new FileUpload($_FILES["file"], $_SERVER);

        $this->assertFalse($this->validator->validate($upload, $file, $_FILES['file']['size']));

	$this->assertEquals(1, count($upload->getErrors()), "Expected the error count to equal 1 since the validation was not successful");
    }
}

<?php

namespace FileUpload\Validator;

use Exception;
use FileUpload\File;
use PHPUnit\Framework\TestCase;

class SizeValidatorTest extends TestCase
{
    protected $directory;
    protected $validator;
    protected $file;

    protected function setUp(): void
    {
        $this->directory = __DIR__ . '/../../fixtures/';

        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];
    }

    public function testNumericMaxSize()
    {
        $this->validator = new SizeValidator(pow(1024, 3));

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertTrue($this->validator->validate($file, $_FILES['file']['size']));
    }

    public function testBetweenMinAndMaxSize()
    {
        $this->validator = new SizeValidator("40K", "10K");

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertTrue($this->validator->validate($file, $_FILES['file']['size']));
    }


    public function testFileSizeTooLarge()
    {
        $this->validator = new SizeValidator("20K", 10);

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertFalse($this->validator->validate($file, $_FILES['file']['size']));
    }

    public function testFileSizeTooSmall()
    {
        $this->validator = new SizeValidator("1M", "50k");

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertFalse($this->validator->validate($file, $_FILES['file']['size']));
    }

    public function testSetMaximumErrorMessages()
    {
        $this->validator = new SizeValidator("29K", "10K");

        $file = new File($_FILES['file']['tmp_name']);

        $fileTooLarge = "Too Large";

        $this->validator->setErrorMessages([
            0 => $fileTooLarge
        ]);

        $this->assertFalse($this->validator->validate($file, $_FILES['file']['size']));

        $this->assertEquals($fileTooLarge, $file->error);
    }

    public function testSetMinimumErrorMessages()
    {
        $this->validator = new SizeValidator("40K", "35K");

        $file = new File($_FILES['file']['tmp_name']);

        $fileTooSmall = "Too Small";

        $this->validator->setErrorMessages([
            1 => $fileTooSmall
        ]);

        $this->assertFalse($this->validator->validate($file, $_FILES['file']['size']));

        $this->assertEquals($fileTooSmall, $file->error);
    }

    public function testInvalidMaximumFileSize()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid File Max_Size");
        $this->validator = new SizeValidator("40A", 3);
    }

    public function testInvalidMinimumFilesSize()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid File Min_Size");
        $this->validator = new SizeValidator("40K", "-3");
    }
}

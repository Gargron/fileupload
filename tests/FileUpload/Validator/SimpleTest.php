<?php

namespace FileUpload\Validator;

use Exception;
use FileUpload\File;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testExceedSize()
    {
        $validator = new Simple(10);
        $file = new File($_FILES['file']['tmp_name']);

        $this->assertFalse($validator->validate($file, $_FILES['file']['size']));
        $this->assertNotEmpty($file->error);
    }

    public function testExceedMaxSize()
    {
        $validator = new Simple("20K");

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertFalse($validator->validate($file, 10));
        $this->assertNotEmpty($file->error);
    }

    public function testFailMaxSize1A()
    {
        $this->expectException(Exception::class);
        $validator = new Simple("1A", []);
    }

    public function testWrongMime()
    {
        $validator = new Simple("1M", ['image/png']);

        $file = new File($_FILES['file']['tmp_name']);

        $this->assertFalse($validator->validate($file, 7));
        $this->assertNotEmpty($file->error);
    }

    public function testOk()
    {
        $validator = new Simple("40K", ['image/jpeg']);
        $file = new File($_FILES['file']['tmp_name']);

        $this->assertTrue($validator->validate($file, $_FILES['file']['size']));
        $this->assertEmpty($file->error);
    }

    public function testSetErrorMessages()
    {
        $file = new File($_FILES['file']['tmp_name']);

        $validator = new Simple(10, ['image/png']);

        $errorMessage = "Invalid file size";

        $validator->setErrorMessages([
            0 => $errorMessage
        ]);

        $validator->validate($file, $_FILES['file']['size']);

        $this->assertEquals($errorMessage, $file->error);
    }

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
}

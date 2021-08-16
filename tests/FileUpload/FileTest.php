<?php

namespace FileUpload;

use Exception;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testImagePositiveDetermination()
    {
        $file = new File(__DIR__ . '/../fixtures/real-image.jpg');

        $this->assertTrue($file->isImage(), 'Detect real image');

        $this->assertEquals('image/jpeg', $file->getMimeType());
    }

    public function testCannotGetMimetypeForADirectory()
    {
        $this->expectException(Exception::class);
        $file = new File(__DIR__ . '/../fixtures');
        $file->getMimeType();
    }

    public function testImageNegativeDetermination()
    {
        $file = new File(__DIR__ . '/../fixtures/fake-image.jpg');

        $this->assertFalse($file->isImage(), 'Detect fake image');
    }
}

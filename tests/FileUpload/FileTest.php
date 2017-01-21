<?php

namespace FileUpload;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testImagePositiveDetermination()
    {
        $file = new File(__DIR__ . '/../fixtures/real-image.jpg');

        $this->assertTrue($file->isImage(), 'Detect real image');

        $this->assertEquals('image/jpeg', $file->getMimeType());
    }


    /**
     * @expectedException \Exception
     */
    public function testCannotGetMimetypeForADirectory()
    {
        $file = new File(__DIR__ . '/../fixtures');
        $file->getMimeType();
    }

    public function testImageNegativeDetermination()
    {
        $file = new File(__DIR__ . '/../fixtures/fake-image.jpg');

        $this->assertFalse($file->isImage(), 'Detect fake image');
    }
}

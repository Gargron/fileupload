<?php

namespace FileUpload;

class FileTest extends \PHPUnit_Framework_TestCase {
  public function testImagePositiveDetermination() {
    $file = new File;
    $file->setTypeFromPath(__DIR__ . '/../fixtures/real-image.jpg');

    $this->assertTrue($file->isImage(), 'Detect real image');
  }

  public function testImageNegativeDetermination() {
    $file = new File;
    $file->setTypeFromPath(__DIR__ . '/../fixtures/fake-image.jpg');

    $this->assertFalse($file->isImage(), 'Detect fake image');
  }
}

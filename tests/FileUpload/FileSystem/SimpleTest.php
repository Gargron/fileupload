<?php

namespace FileUpload\FileSystem;

class SimpleTest extends \PHPUnit_Framework_TestCase {
  protected $filesystem;

  public function setUp() {
    $playground_path = __DIR__ . '/../../playground';
    $fixtures_path   = __DIR__ . '/../../fixtures';

    $this->filesystem = new Simple();

    if(! is_dir($playground_path)) {
      mkdir($playground_path);
    }

    if(! is_file($playground_path . '/yadda.txt')) {
      copy($fixtures_path . '/yadda.txt', $playground_path . '/yadda.txt');
    }
  }

  public function testIsFile() {
    $this->assertTrue($this->filesystem->isFile(__DIR__ . '/../../fixtures/real-image.jpg'));
    $this->assertFalse($this->filesystem->isFile(__DIR__ . '/../../fixtures'));
  }

  public function testIsDir() {
    $this->assertFalse($this->filesystem->isDir(__DIR__ . '/../../fixtures/real-image.jpg'));
    $this->assertTrue($this->filesystem->isDir(__DIR__ . '/../../fixtures'));
  }

  public function testWriteToFile() {
    $yadda = __DIR__ . '/../../playground/yadda.txt';
    $path  = __DIR__ . '/../../playground/test.1.txt';

    $this->filesystem->writeToFile($path, $this->filesystem->getFileStream($yadda));
    $this->assertEquals(file_get_contents($yadda), file_get_contents($path));

    $this->filesystem->unlink($path);
  }

  public function testMoveUploadedFile() {
    $yadda = __DIR__ . '/../../playground/yadda.txt';
    $path  = __DIR__ . '/../../playground/test.2.txt';

    $original_yadda = file_get_contents($yadda);

    $this->filesystem->moveUploadedFile($yadda, $path);
    $this->assertEquals($original_yadda, file_get_contents($path));

    $this->filesystem->moveUploadedFile($path, $yadda);
  }

  public function testGetFilesize() {
    $this->assertEquals(20 * 1024, $this->filesystem->getFilesize(__DIR__ . '/../../fixtures/blob'));
  }
}

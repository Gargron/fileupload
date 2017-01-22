<?php

namespace FileUpload;

class FileUploadTest extends \PHPUnit_Framework_TestCase {
  public function setUp() {
    $playground_path = __DIR__ . '/../playground';
    $fixtures_path   = __DIR__ . '/../fixtures';

    if(! is_dir($playground_path)) {
      mkdir($playground_path);
    }

    if(! is_dir($playground_path . '/uploaded')) {
      mkdir($playground_path . '/uploaded');
    }

    if(! is_file($playground_path . '/real-image.jpg')) {
      copy($fixtures_path . '/real-image.jpg', $playground_path . '/real-image.jpg');
    }

    if(is_file($playground_path . '/uploaded/real-image.jpg')) {
      unlink($playground_path . '/uploaded/real-image.jpg');
    }
  }

  public function testSingleUpload() {
    $playground_path = __DIR__ . '/../playground';

    $server = array('CONTENT_TYPE' => 'image/jpg', 'CONTENT_LENGTH' => 30321);
    $file   = array('tmp_name' => $playground_path . '/real-image.jpg', 'name' => 'real-image.jpg', 'size' => 30321, 'type' => 'image/jpg', 'error' => 0);

    $filesystem = new FileSystem\Mock();
    $resolver   = new PathResolver\Simple($playground_path . '/uploaded');
    $uploader   = new FileUpload($file, $server);
    $test       = false;

    $uploader->setPathResolver($resolver);
    $uploader->setFileSystem($filesystem);

    $uploader->addCallback('completed', function () use (&$test) {
      $test = true;
    });

    list($files, $headers) = $uploader->processAll();

    $this->assertCount(1, $files, 'Files array should contain one file');
    $this->assertEquals(0, $files[0]->error, 'Uploaded file should not have errors');
    $this->assertTrue($test, 'Complete callback should set $test to true');
  }

  // TODO: Tests for multiple uploads, chunked uploads, aborted uploads
}

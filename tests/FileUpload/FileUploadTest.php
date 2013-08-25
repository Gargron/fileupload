<?php

namespace FileUpload;

class FileUploadTest extends \PHPUnit_Framework_TestCase {
  public function testSingleUpload() {
    $server = array();
    $file   = array();

    $filesystem = new FileSystem\Mock();
    $resolver   = new PathResolver\Simple(__DIR__ . '/../fixtures');
    $uploader   = new FileUpload($file, $server);

    $uploader->setPathResolver($resolver);
    $uploader->setFileSystem($filesystem);
  }
}

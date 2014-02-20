<?php

namespace FileUpload;

class FileUploadFactoryTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $factory  = new FileUploadFactory(new PathResolver\Simple(''), new FileSystem\Mock());
    $instance = $factory->create(array(), array());

    $this->assertTrue($instance instanceof FileUpload);
  }
}

<?php

namespace FileUpload;

class FileUploadFactoryTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $factory  = new FileUploadFactory(new Validator\Simple(0, array()), new PathResolver\Simple(''), new FileSystem\Mock());
    $instance = $factory->create(array(), array());

    $this->assertTrue($instance instanceof FileUpload);
  }
}
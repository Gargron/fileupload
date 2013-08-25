<?php

namespace FileUpload\Validator;
use FileUpload\File;

class SimpleTest extends \PHPUnit_Framework_TestCase {
  public function testExceedSize() {
    $validator = new Simple(10, array());
    $file = new File;
    $file->size = 11;

    $this->assertFalse($validator->validate('', $file, 11));
    $this->assertNotEmpty($file->error);
  }

  public function testWrongMime() {
    $validator = new Simple(10, array('image/png'));
    $file = new File;
    $file->type = 'application/json';

    $this->assertFalse($validator->validate('', $file, 11));
    $this->assertNotEmpty($file->error);
  }

  public function testOk() {
    $validator = new Simple(10, array('image/png'));
    $file = new File;
    $file->size = 10;
    $file->type = 'image/png';

    $this->assertTrue($validator->validate('', $file, 10));
    $this->assertEmpty($file->error);
  }
}

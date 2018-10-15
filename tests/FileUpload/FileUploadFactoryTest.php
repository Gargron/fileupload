<?php

namespace FileUpload;

use PHPUnit\Framework\TestCase;

class FileUploadFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory  = new FileUploadFactory(new PathResolver\Simple(''), new FileSystem\Mock());
        $instance = $factory->create(array(), array());

        $this->assertTrue($instance instanceof FileUpload);
    }
}

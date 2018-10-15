<?php

namespace FileUpload\PathResolver;

use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testUploadPath()
    {
        $resolver = new Simple(__DIR__ . '/../../fixtures');
        $this->assertEquals(__DIR__ . '/../../fixtures/real-image.jpg', $resolver->getUploadPath('real-image.jpg'));
    }

    public function testUpcountName()
    {
        $resolver = new Simple(__DIR__ . '/../../fixtures');
        $this->assertEquals('real-image (1).jpg', $resolver->upcountName('real-image.jpg'));
        $this->assertEquals('real-image (2).jpg', $resolver->upcountName('real-image (1).jpg'));
    }
}

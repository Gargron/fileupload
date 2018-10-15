<?php

namespace FileUpload\Validator;

use FileUpload\File;
use PHPUnit\Framework\TestCase;

class DimensionValidatorTest extends TestCase
{
    protected $directory;

    public function testOnlyAnImageCanBeValidated()
    {
        $_FILES['file'] = [
            "name" => "fake-image.jpg",
            "tmp_name" => $this->directory . 'fake-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'width' => 100,
            'height' => 200
        ];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );

        $this->assertEquals(
            'Cannot validate the currently uploaded file by it\'s dimension as it is not an image',
            $file->error
        );
    }

    protected function createValidator(array $config)
    {
        return new DimensionValidator($config);
    }

    public function testValidatorWorksWithTheWidthConfig()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'width' => 300
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );

        $config = [
            'width' => 302
        ];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testValidatorWorksWithTheMinimumWidthConfig()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'min_width' => 200
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );

        $config = [
            'min_width' => 301
        ];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testValidatorWorksWithTheMaximumWidthConfig()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'max_width' => 400
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );

        $config = [
            'max_width' => 250
        ];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testValidatorWorksExpectedlyWithTheWidthAndHeightValues()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'width' => 300,
            'height' => 300
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testValidatorWorksWithTheHeightConfig()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'height' => 300
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );

        $config = [
            'height' => 305
        ];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testValidatorWorksWithTheMinimumHeightConfig()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'min_height' => 200
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );

        $config = [
            'min_height' => 301
        ];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testValidatorWorksWithTheMaximumHeightConfig()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'max_height' => 400
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );

        $config = [
            'max_height' => 209
        ];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testValidatorWorksAsExpectedWithAllConfigOption()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'width' => 300,
            'height' => 300,
            'max_width' => 310,
            'max_height' => 350
        ];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($file, $_FILES['file']['size'])
        );
    }

    public function testSetErrorMessages()
    {
        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $file = new File($_FILES['file']['tmp_name']);

        $config = [
            'width' => 301,
            'height' => 301
        ];

        $validator = $this->createValidator($config);

        $validator->setErrorMessages([
            DimensionValidator::HEIGHT => "Height too large"
        ]);

        $validator->validate($file, $_FILES['file']['size']);

        $this->assertEquals(
            'Height too large',
            $file->error
        );
    }

    protected function setUp()
    {
        $this->directory = __DIR__ . '/../../fixtures/';
    }
}

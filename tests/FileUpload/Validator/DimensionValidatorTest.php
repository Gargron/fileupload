<?php

namespace FileUpload\Tests\Validator;

use FileUpload\File;
use FileUpload\FileUpload;
use FileUpload\Validator\DimensionValidator;
use PHPUnit\Framework\TestCase;

class DimensionValidatorTest extends TestCase
{
    protected $directory;

    protected $file;

    protected function setUp()
    {
        $this->directory = dirname(__DIR__, 2). DIRECTORY_SEPARATOR. "fixtures".DIRECTORY_SEPARATOR;

        $_FILES['file'] = [
             "name" => "real-image.jpg",
            "tmp_name" => $this->directory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $this->file = new File($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    }

    public function testOnlyAnImageCanBeValidated()
    {
        $_FILES['fake'] = [
            "name" => "fake.jpg",
            "tmp_name" => $this->directory . 'fake-image.jpg',
            "size" => 12,
            "error" => 0
        ];

        $config = [
            'width' => 100,
            'height' => 200
        ];

	$this->file = new File($_FILES['fake']['tmp_name'], $_FILES['fake']['name']);

        $upload = new FileUpload($_FILES['fake'], $_SERVER);

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['fake']['size'])
        );

        $this->assertEquals(1, count($upload->getErrors()));
    }

    protected function createValidator(array $config): DimensionValidator
    {
        return new DimensionValidator($config);
    }

    public function testValidatorWorksWithTheWidthConfig()
    {
        $config = ['width' => 300];

        $upload = new FileUpload($_FILES['file'], $_SERVER);

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
    );
        $this->assertEquals(0, count($upload->getErrors()));

        $config = ['width' => 301];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
    );

        $this->assertEquals(1, count($upload->getErrors()));
    }

    public function testValidatorWorksWithTheMinimumWidthConfig()
    {
        $config = ['min_width' => 200];

        $upload = new FileUpload($_FILES['file'], $_SERVER);

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(0, count($upload->getErrors()));

        $config = [ 'min_width' => 301];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );

        $this->assertEquals(1, count($upload->getErrors()));
    }

    public function testValidatorWorksWithTheMaximumWidthConfig()
    {
        $config = [ 'max_width' => 400];

        $upload = new FileUpload($_FILES['file'], $_SERVER);

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(0, count($upload->getErrors()));

        $config = [ 'max_width' => 250];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(1, count($upload->getErrors()));
    }

    public function testValidatorWorksExpectedlyWithTheWidthAndHeightValues()
    {
        $config = [
            'width' => 300,
            'height' => 300
        ];

        $upload = new FileUpload($_FILES['file'], $_SERVER);

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(0, count($upload->getErrors()));
    }

    public function testValidatorWorksWithTheHeightConfig()
    {
        $upload = new FileUpload($_FILES['file'], $_SERVER);
        $config = [ 'height' => 300];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(0, count($upload->getErrors()));

        $config = [ 'height' => 305];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(1, count($upload->getErrors()));
    }

    public function testValidatorWorksWithTheMinimumHeightConfig()
    {
        $upload = new FileUpload($_FILES['file'], $_SERVER);

        $config = [ 'min_height' => 200];

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(0, count($upload->getErrors()));

        $config = [ 'min_height' => 301];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(1, count($upload->getErrors()));
    }

    public function testValidatorWorksWithTheMaximumHeightConfig()
    {
        $config =['max_height' => 400];

        $upload = new FileUpload($_FILES['file'], $_SERVER);

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );

        $this->assertEquals(0, count($upload->getErrors()));

        $config = [ 'max_height' => 209];

        $this->assertFalse(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(1, count($upload->getErrors()));
    }

    public function testValidatorWorksAsExpectedWithAllConfigOption()
    {
        $config = [
            'width' => 300,
            'height' => 300,
            'max_width' => 310,
            'max_height' => 350
        ];

        $upload = new FileUpload($_FILES['file'], $_SERVER);

        $this->assertTrue(
            $this->createValidator($config)
                ->validate($upload, $this->file, $_FILES['file']['size'])
        );
        $this->assertEquals(0, count($upload->getErrors()));
    }
}

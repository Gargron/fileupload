<?php

namespace FileUpload\Tests\Validator;

use FileUpload\FileUpload;
use FileUpload\File;
use PHPUnit\Framework\TestCase;
use FileUpload\Validator\SizeValidator;

class SizeValidatorTest extends TestCase
{

    /**
     * @var FileUpload
     */
    protected $upload;

    protected function setUp()
    {
        $testFixturesDIrectory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "fixtures". DIRECTORY_SEPARATOR;

        $_FILES['file'] = [
            "name" => "real-image.jpg",
            "tmp_name" => $testFixturesDIrectory . 'real-image.jpg',
            "size" => 12,
            "error" => 0
    ];

        $this->upload = new FileUpload($_FILES['file'], $_SERVER);
    }

    public function testNumericMaxSize()
    {
        $validator = new SizeValidator(pow(1024, 3));

        $file = new File($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        $this->assertTrue($validator->validate($this->upload, $file, $_FILES['file']['size']));
    }

    public function testBetweenMinAndMaxSize()
    {
        $validator = new SizeValidator("40KB", "10KB");

        $file = new File($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        $this->assertTrue($validator->validate($this->upload, $file, $_FILES['file']['size']));
    }


    public function testFileSizeTooLarge()
    {
        $validator = new SizeValidator("20KB", 10);

        $file = new File($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        $this->assertFalse($validator->validate($this->upload, $file, $_FILES['file']['size']));
    }

    public function testFileSizeTooSmall()
    {
        $validator = new SizeValidator("1MB", "50KB");

        $file = new File($_FILES['file']['tmp_name'], $_FILES['file']['name']);

        $this->assertFalse($validator->validate($this->upload, $file, $_FILES['file']['size']));
    }

    /**
     * @dataProvider getInvalidSizeFixtures
     * @expectedException \FileUpload\Util\HumanReadableToBytesException
     */
    public function testInvalidMaximumFileSizeUnit(string $humanReadableSize)
    {
        $validator = new SizeValidator($humanReadableSize, 3);
    }

    /**
     * @dataProvider getInvalidSizeFixtures
     * @expectedException \FileUpload\Util\HumanReadableToBytesException
     */
    public function testInvalidMinimumFileSizeUnit(string $humanReadableSize)
    {
        $validator = new SizeValidator("40K", $humanReadableSize);
    }

    public function getInvalidSizeFixtures()
    {
        return [
            ["40A"],
            ["20Z"]
        ];
    }

    /**
     * @expectedException \FileUpload\Util\HumanReadableToBytesException
     */
    public function testMaximumFileSizeMustBeGreaterThanZero()
    {
        $validator = new SizeValidator(-1, "40K");
    }

    /**
     * @expectedException \FileUpload\Util\HumanReadableToBytesException
     */
    public function testMinimumFileSizeMustBeGreaterThanZero()
    {
        $validator = new SizeValidator("40K", -1);
    }
}

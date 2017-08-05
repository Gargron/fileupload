<?php

namespace FileUpload\Tests\FileUpload\Util;

use FileUpload\Util\Size;
use PHPUnit\Framework\TestCase;

class SizeTest extends TestCase
{

    /**
     * @dataProvider getInvalidInputs
     * @expectedException \FileUpload\Util\HumanReadableToBytesException
     */
    public function testCannotParseAnUnsupportedUnit(string $input)
    {
        Size::fromHumanReadable($input);
    }

    public function getInvalidInputs(): array
    {
        return [
            ["1BB"],
            ["1K"],
            ["1KBB"],
            ["10M"],
            ["10MBB"],
            ["20G"],
            ["20GBB"]
        ];
    }

    /**
     * @dataProvider getValidInputs
     */
    public function testItSuccessfullyParsesAnHumanReadableString(string $input, int $expected)
    {
        $this->assertEquals(Size::fromHumanReadable($input), $expected);
    }

    public function getValidInputs(): array
    {
        return [
            ["1B", 1],
            ["10B", 10],
            ["1KB", 1024],
            ["15KB", 15360],
            ["1MB", 1048576],
            ["15MB",15728640],
            ["1GB", 1073741824],
            ["2GB", 2147483648]
        ];
    }
}

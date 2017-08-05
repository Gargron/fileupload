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
}

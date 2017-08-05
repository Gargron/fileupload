<?php

namespace FileUpload\Tests\FileUpload\Util;

use FileUpload\Util\MimeType;
use PHPUnit\Framework\TestCase;

class MimeTypeTest extends TestCase
{

    /**
     * @dataProvider getFixtures
     *
     */
    public function testMimeTypeIsGottenFromAPath(string $path, string $expectedMimeType)
    {
        $this->assertEquals($expectedMimeType, MimeType::fromFile($path), "Mimetypes are not equal");
    }

    public function getFixtures(): array
    {
        $fixturesPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR. "fixtures". DIRECTORY_SEPARATOR;

        return [
               [ $fixturesPath."real-image.jpg","image/jpeg"],
	       [ $fixturesPath."yadda.txt", "text/plain"],
        ];
    }
}

<?php

namespace FileUpload\Util;

class Size
{
    const BYTES_PER_KILOBYTES = 1024;

    const REGEX = "/(\d+)([KMGT]B$|B$)/";

    public static function fromHumanReadable(string $input): int
    {
        $matches = [];

        if (!preg_match(self::REGEX, $input, $matches)) {
            throw new HumanReadableToBytesException("Unknown/Invalid unit");
        }

        $knownUnits = [
            'B' => 1,
            'KB' => pow(self::BYTES_PER_KILOBYTES, 1),
            'MB' => pow(self::BYTES_PER_KILOBYTES, 2),
            'GB' => pow(self::BYTES_PER_KILOBYTES, 3)
        ];

        // $matches = [0 => "2MB", 1 => "2", 2 => "MB"]
        return ((int)$matches[1]) * $knownUnits[mb_strtoupper($matches[2])];
    }
}

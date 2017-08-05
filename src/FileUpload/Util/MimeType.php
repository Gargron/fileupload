<?php

namespace FileUpload\Util;

class Mimetype
{
    public static function fromFile(string $path): string
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }
}

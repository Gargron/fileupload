<?php

namespace FileUpload\Validator;

use FileUpload\FileUpload;
use FileUpload\File;

interface ValidatorInterface
{
    public function validate(FileUpload $upload, File $file, $currentSize = -1): bool;
}

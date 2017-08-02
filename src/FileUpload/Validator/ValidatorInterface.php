<?php

namespace FileUpload\Validator;

use FileUpload\FileUpload;
use FileUpload\FileInterface;

interface ValidatorInterface
{
    public function validate(FileUpload $upload, FileInterface $file, $currentSize = -1);
}

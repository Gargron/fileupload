<?php

namespace FileUpload\Validator;

use FileUpload\File;
use FileUpload\FileUpload;

class MimeTypeValidator implements ValidatorInterface
{
    protected $validMimeTypes;

    public function __construct(array $validMimeTypes)
    {
        $this->validMimeTypes = $validMimeTypes;
    }

    public function validate(FileUpload $upload, File $file, $currentSize = -1): bool
    {
        if (in_array($file->getMimeType(), $this->validMimeTypes, true)) {
            return true;
        }

        $upload->addError(sprintf("%s has an invalid mimetype (%s)", $file->getName(), $file->getMimeType()));
        return false;
    }
}

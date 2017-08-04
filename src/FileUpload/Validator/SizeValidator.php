<?php

namespace FileUpload\Validator;

use FileUpload\File;
use FileUpload\FileUpload;
use FileUpload\Util;

class SizeValidator implements ValidatorInterface
{
    protected $maxFileSize;

    protected $minFileSize;

    public function __construct($maxSize, $minSize = 0)
    {
        $this->minFileSize = $this->bytes($minSize);
        $this->maxFileSize = $this->bytes($maxSize);

	$this->validateSizes();
    }

    private function validateSizes()
    {
        if ($this->maxFileSize <= 0) {
            throw new SizeValidatorException("Invalid max size. Max size can be equal or be less than zero (0)");
        }

        if ($this->minFileSize < 0) {
            throw new SizeValidatorException("Invalid min size. Min size can be lesser than zero (0)");
        }
    }

    protected function bytes($size)
    {
        // We would assume it is already in a valid format. Something like "1024"
        if (is_numeric($size)) {
            return $size;
        }

        return Util::humanReadableToBytes($size);
    }

    public function validate(FileUpload $upload, File $file, $currentSize = -1): bool
    {
        if ($file->getSize() < $this->minFileSize) {
            $upload->addError("The uploaded file is too small");
            return false;
        }

        if ($file->getSize() > $this->maxFileSize || $currentSize > $this->maxFileSize) {
            $upload->addError("The uploaded file is too large");
            return false;
        }

        return true ;
    }
}

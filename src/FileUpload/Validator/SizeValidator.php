<?php

namespace FileUpload\Validator;

use FileUpload\File;
use FileUpload\Util;

class SizeValidator implements Validator
{

    const FILE_SIZE_IS_TOO_LARGE = 0;
    const FILE_SIZE_IS_TOO_SMALL = 1;

    /**
     * @var int The maximum file size of the uploaded file
     */
    protected $maxSize;

    /**
     * @var int The minimum file size of the uploaded file
     */
    protected $minSize;

    /**
     * @var bool Determines the upload status of the file
     */
    protected $isValid;

    protected $errorMessages = array(
        self::FILE_SIZE_IS_TOO_LARGE => "The uploaded file is too large",
        self::FILE_SIZE_IS_TOO_SMALL => "The uploaded file is too small"
    );

    /**
     * @param int $maxSize
     * @param int $minSize Defaults to 0
     */
    public function __construct($maxSize, $minSize = 0)
    {
        $this->maxSize = $this->setMaxSize($maxSize);
        $this->minSize = $this->setMinFile($minSize);
        $this->isValid = true;
    }

    /**
     * @param $maxSize
     * @return int|string
     * @throws \Exception if the max file size is null or equals zero
     */
    public function setMaxSize($maxSize)
    {
        $max = 0;

        if (is_numeric($maxSize)) {
            $max = $maxSize;
        } else {
            $max = Util::humanReadableToBytes($maxSize);
        }

        if ($max < 0 || $max === null) {
            throw new \Exception("Invalid File Max_Size");
        }

        return $max;
    }


    /**
     * @param $minSize
     * @return int|string
     * @throws \Exception if the file size is lesser than zero or null
     */
    public function setMinFile($minSize)
    {
        $min = 0;

        if (is_numeric($minSize)) {
            $min = $minSize;
        } else {
            $min = Util::humanReadableToBytes($minSize);
        }

        if ($min < 0 || $min === null) {
            throw new \Exception("Invalid File Min_Size");
        }

        return $min;
    }

    /**
     * {@inheritdoc}
     */
    public function setErrorMessages(array $messages)
    {
        foreach ($messages as $key => $value) {
            $this->errorMessages[$key] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(File $file, $currentSize = null)
    {
        if ($file->getSize() < $this->minSize) {
            $file->error = $this->errorMessages[self::FILE_SIZE_IS_TOO_SMALL];
            $this->isValid = false;
        }

        if ($file->getSize() > $this->maxSize || $currentSize > $this->maxSize) {
            $file->error = $this->errorMessages[self::FILE_SIZE_IS_TOO_LARGE];
            $this->isValid = false;
        }

        return $this->isValid;
    }
}

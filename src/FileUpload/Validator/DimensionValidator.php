<?php

namespace FileUpload\Validator;

use FileUpload\File;
use FileUpload\FileUpload;

class DimensionValidator implements ValidatorInterface
{
    const INVALID_UPLOADED_FILE_TYPE = 0;

    const WIDTH = 'width';
    const MAX_WIDTH = 'max_width';
    const MIN_WIDTH = 'min_width';
    const HEIGHT = 'height';
    const MAX_HEIGHT = 'max_height';
    const MIN_HEIGHT = 'min_height';

    protected $config;

    protected $errorMessages = [
        self::INVALID_UPLOADED_FILE_TYPE => "Cannot validate the currently uploaded file by it's dimension as it is not an image",

        self::HEIGHT => "The uploaded file's height is invalid. It should have an height of {value}",

        self::MIN_HEIGHT => "The uploaded file's height is too small. It should have a minimum height of {value}",

        self::MAX_HEIGHT => "The uploaded file's height is too large. It should have a maximum height of {value}",

        self::WIDTH => "The uploaded file's width is invalid. It should have an height of {value}",

        self::MIN_WIDTH => "The uploaded file's width is too small. It should have a minimum height of {value}",

        self::MAX_WIDTH => "The uploaded file's width is too large. It should have a maximum height of {value}"
    ];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function validate(FileUpload $upload, File $file, $currentSize = null): bool
    {
        if (!$file->isImage() || !list($width, $height) = getimagesize($file->getRealPath())) {
            $upload->addError($file->getName(). " : ". $this->errorMessages[self::INVALID_UPLOADED_FILE_TYPE]);
            return false;
        }

        return $this->validateDimensions($upload, $width, $height);
    }

    protected function validateDimensions(FileUpload $upload, $width, $height): bool
    {
        //Multiple if/else here so as to allow proper message formatting.
        //All can be lumped up in one big if check but the error message would be poor.
        //Plus that would defeat the ability of users setting up custom error messages

        if (isset($this->config[self::WIDTH]) && $this->config[self::WIDTH] !== $width) {
            $upload->addError($this->formatErrorMessage(self::WIDTH, $this->config[self::WIDTH]));
            return false;
        }

        if (isset($this->config[self::MIN_WIDTH]) && $this->config[self::MIN_WIDTH] > $width) {
            $upload->addError($this->formatErrorMessage(self::MIN_WIDTH, $this->config[self::MIN_WIDTH]));
            return false;
        }

        if (isset($this->config[self::MAX_WIDTH]) && $this->config[self::MAX_WIDTH] < $width) {
            $upload->addError($this->formatErrorMessage(self::MAX_WIDTH, $this->config[self::MAX_WIDTH]));
            return false;
        }

        if (isset($this->config[self::HEIGHT]) && $this->config[self::HEIGHT] !== $height) {
            $upload->addError($this->formatErrorMessage(self::HEIGHT, $this->config[self::HEIGHT]));
            return false;
        }

        if (isset($this->config[self::MIN_HEIGHT]) && $this->config[self::MIN_HEIGHT] > $height) {
            $upload->addError($this->formatErrorMessage(self::MIN_HEIGHT, $this->config[self::MIN_HEIGHT]));
            return false;
        }

        if (isset($this->config[self::MAX_HEIGHT]) && $this->config[self::MAX_HEIGHT] < $height) {
            $upload->addError($this->formatErrorMessage(self::MAX_HEIGHT, $this->config[self::MAX_HEIGHT]));
            return false;
        }

        return true;
    }

    protected function formatErrorMessage(string $key, int $val): string
    {
        return $this->errorMessages[$key] = str_replace(
            "{value}",
            $val,
            $this->errorMessages[$key]
        );
    }
}

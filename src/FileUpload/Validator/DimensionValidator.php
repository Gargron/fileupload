<?php

namespace FileUpload\Validator;

use FileUpload\File;

class DimensionValidator implements Validator
{

    const INVALID_UPLOADED_FILE_TYPE = 0;

    const WIDTH = 'width';
    const MAX_WIDTH = 'max_width';
    const MIN_WIDTH = 'min_width';
    const HEIGHT = 'height';
    const MAX_HEIGHT = 'max_height';
    const MIN_HEIGHT = 'min_height';

    protected $config;

    protected $errorMessages = array(

        self::INVALID_UPLOADED_FILE_TYPE => "Cannot validate the currently uploaded file by it's dimension as it is not an image",

        self::HEIGHT => "The uploaded file's height is invalid. It should have an height of {value}",

        self::MIN_HEIGHT => "The uploaded file's height is too small. It should have a minimum height of {value}",

        self::MAX_HEIGHT => "The uploaded file's height is too large. It should have a maximum height of {value}",

        self::WIDTH => "The uploaded file's width is invalid. It should have an height of {value}",

        self::MIN_WIDTH => "The uploaded file's width is too small. It should have a minimum height of {value}",

        self::MAX_WIDTH => "The uploaded file's width is too large. It should have a maximum height of {value}"
    );

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function setErrorMessages(array $message)
    {
        foreach ($message as $key => $value) {
            $this->errorMessages[$key] = $value;
        }
    }

    public function validate(File $file, $currentSize = null)
    {

        if (!$file->isImage() || !list($width, $height) = getimagesize($file->getRealPath())) {
            $file->error = $this->errorMessages[self::INVALID_UPLOADED_FILE_TYPE];

            return false;
        }

        return $this->validateDimensions($file, $width, $height);
    }

    protected function validateDimensions(File $file, $width, $height)
    {
        $valid = true;

        //Multiple if/else here so as to allow proper message formatting.
        //All can be lumped up in one big if check but the error message would be poor.
        //Plus that would defeat the ability of users setting up custom error messages

        if (isset($this->config[self::WIDTH]) && $this->config[self::WIDTH] !== $width) {
            $file->error = $this->formatErrorMessage(self::WIDTH, $this->config[self::WIDTH]);
            $valid = false;
        }

        if (isset($this->config[self::MIN_WIDTH]) && $this->config[self::MIN_WIDTH] > $width) {
            $file->error = $this->formatErrorMessage(self::MIN_WIDTH, $this->config[self::MIN_WIDTH]);
            $valid = false;
        }

        if (isset($this->config[self::MAX_WIDTH]) && $this->config[self::MAX_WIDTH] < $width) {
            $file->error = $this->formatErrorMessage(self::MAX_WIDTH, $this->config[self::MAX_WIDTH]);
            $valid = false;
        }

        if (isset($this->config[self::HEIGHT]) && $this->config[self::HEIGHT] !== $height) {
            $file->error = $this->formatErrorMessage(self::HEIGHT, $this->config[self::HEIGHT]);
            $valid = false;
        }

        if (isset($this->config[self::MIN_HEIGHT]) && $this->config[self::MIN_HEIGHT] > $height) {
            $file->error = $this->formatErrorMessage(self::MIN_HEIGHT, $this->config[self::MIN_HEIGHT]);
            $valid = false;
        }

        if (isset($this->config[self::MAX_HEIGHT]) && $this->config[self::MAX_HEIGHT] < $height) {
            $file->error = $this->formatErrorMessage(self::MAX_HEIGHT, $this->config[self::MAX_HEIGHT]);
            $valid = false;
        }

        return $valid;
    }

    protected function formatErrorMessage($key, $heightValue)
    {
        return $this->errorMessages[$key] = str_replace(
            "{value}",
            $heightValue,
            $this->errorMessages[$key]
        );
    }
}

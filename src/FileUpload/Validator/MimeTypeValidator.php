<?php

namespace FileUpload\Validator;

use FileUpload\File;

class MimeTypeValidator implements Validator
{

    const INVALID_MIMETYPE = 0;

    /**
     * List of mimetypes to be considered valid
     * @var array
     */
    protected $mimeTypes;

    /**
     * Determines if the upload was successful or nay
     * @var bool
     */
    protected $isValid;

    /**
     * Default error message for this Validator
     * @var array
     */
    protected $errorMessages = array(
        self::INVALID_MIMETYPE => "The uploaded filetype (mimetype) is invalid"
    );

    public function __construct(array $validMimeTypes)
    {
        $this->mimeTypes = $validMimeTypes;
        $this->isValid = true; //Innocent (Valid file) unless proven otherwise :)
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
        if (!in_array($file->getMimeType(), $this->mimeTypes)) {
            $this->isValid = false;
            $file->error = $this->errorMessages[self::INVALID_MIMETYPE];
        }

        return $this->isValid;
    }
}

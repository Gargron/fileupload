<?php

namespace FileUpload\Validator;

use FileUpload\File;
use FileUpload\Util;

class Simple implements Validator
{
    /**
     * Our errors
     */
    const UPLOAD_ERR_BAD_TYPE = 0;
    const UPLOAD_ERR_TOO_LARGE = 1;
    /**
     * Max allowed file size
     * @var integer
     */
    protected $max_size;
    /**
     * Allowed mime types
     * @var array
     */
    protected $allowed_types;
    /**
     * Error messages
     * @var array
     */
    protected $messages = array(
        self::UPLOAD_ERR_BAD_TYPE => 'Filetype not allowed',
        self::UPLOAD_ERR_TOO_LARGE => 'Filesize too large',
    );

    /**
     * @param integer $max_size
     * @param array   $allowed_types
     */
    public function __construct($max_size, array $allowed_types = array())
    {
        $this->setMaxSize($max_size);
        $this->allowed_types = $allowed_types;
    }

    /**
     * Sets the max file size
     * @param mixed $max_size
     * @throws \Exception if the max_size value is invalid
     */
    public function setMaxSize($max_size)
    {
        if (is_numeric($max_size)) {
            $this->max_size = $max_size;
        } else {
            $this->max_size = Util::humanReadableToBytes($max_size);
        }

        if ($this->max_size < 0 || $this->max_size == null) {
            throw new \Exception('invalid max_size value');
        }

    }

    /**
     * {@inheritdoc}
     */
    public function setErrorMessages(array $new_messages)
    {
        foreach ($new_messages as $key => $value) {
            $this->messages[$key] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(File $file, $current_size = null)
    {
        if (!empty($this->allowed_types)) {
            if (!in_array($file->getMimeType(), $this->allowed_types)) {
                $file->error = $this->messages[self::UPLOAD_ERR_BAD_TYPE];

                return false;
            }
        }

        if ($file->getSize() > $this->max_size || $current_size > $this->max_size) {
            $file->error = $this->messages[self::UPLOAD_ERR_TOO_LARGE];

            return false;
        }

        return true;
    }
}

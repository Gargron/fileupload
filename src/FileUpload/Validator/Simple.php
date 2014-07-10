<?php

namespace FileUpload\Validator;
use FileUpload\File;
use FileUpload\Util;

class Simple implements Validator {
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
   * Our errors
   */
  const UPLOAD_ERR_BAD_TYPE  = 0;
  const UPLOAD_ERR_TOO_LARGE = 1;

  /**
   * Error messages
   * @var array
   */
  protected $messages = array(
    self::UPLOAD_ERR_BAD_TYPE  => 'Filetype not allowed',
    self::UPLOAD_ERR_TOO_LARGE => 'Filesize too large',
  );

  /**
   * Ich bin konstruktor
   * @param integer $max_size
   * @param array   $allowed_types
   */
  public function __construct($max_size, array $allowed_types) {
    $this->setMaxSize($max_size);
    $this->allowed_types = $allowed_types;
  }

  /**
   * Merge (overwrite) default messages
   * @param array $new_messages
   */
  public function setMessages(array $new_messages) {
    $this->messages = array_merge($this->messages, $new_messages);
  }

    /**
     * Sets the max file size
     * @param mixed $max_size
     * @throws \Exception if the max_size value is invalid
     */
    public function setMaxSize($max_size) {
        if (is_numeric($max_size)) {
            $this->max_size      = $max_size;
        } else {
            $this->max_size = Util::humanReadableToBytes($max_size);
        }

        if ($this->max_size < 0 ||$this->max_size == null) {
            throw new \Exception('invalid max_size value');
        }
    }


  /**
   * @see Validator
   */
  public function validate($tmp_name, File $file, $current_size) {
    if(! in_array($file->type, $this->allowed_types)) {
      $file->error = $this->messages[self::UPLOAD_ERR_BAD_TYPE];
      return false;
    }

    if($file->size > $this->max_size || $current_size > $this->max_size) {
      $file->error = $this->messages[self::UPLOAD_ERR_TOO_LARGE];
      return false;
    }

    return true;
  }
}

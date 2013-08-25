<?php

namespace FileUpload\Validator;
use FileUpload\File;

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
   * Ich bin konstruktor
   * @param integer $max_size
   * @param array   $allowed_types
   */
  public function __construct($max_size, array $allowed_types) {
    $this->max_size      = $max_size;
    $this->allowed_types = $allowed_types;
  }

  /**
   * @see Validator
   */
  public function validate($tmp_name, File $file, $current_size) {
    if(! in_array($file->type, $this->allowed_types)) {
      $file->error = 'Unallowed file type';
      return false;
    }

    if($file->size > $this->max_size || $current_size > $this->max_size) {
      $file->error = 'Too big for us';
      return false;
    }

    return true;
  }
}

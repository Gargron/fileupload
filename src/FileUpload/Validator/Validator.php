<?php

namespace FileUpload\Validator;
use FileUpload\File;

interface Validator {
  /**
   * Validate upload
   * @param  string  $tmp_name
   * @param  File    $file
   * @param  integer $current_size
   * @return boolean
   */
  public function validate($tmp_name, File $file, $current_size);
}

<?php

namespace FileUpload;

class File {
  /**
   * Preset no errors
   * @var mixed
   */
  public $error = 0;

  /**
   * Preset unknown mime type
   * @var string
   */
  public $type  = 'application/octet-stream';

  /**
   * Determine file type from path (actual mime type, not extension checking)
   * @param string $path
   */
  public function setTypeFromPath($path) {
    $this->type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
  }

  /**
   * Does this file have an image mime type?
   * @return boolean
   */
  public function isImage() {
    return in_array($this->type, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'));
  }
}

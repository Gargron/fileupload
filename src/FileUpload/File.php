<?php

namespace FileUpload;

class File {
  public function setTypeFromPath($path) {
    $this->type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
  }

  public function isImage() {
    return in_array($this->type, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'));
  }
}

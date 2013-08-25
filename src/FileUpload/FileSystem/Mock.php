<?php

namespace FileUpload\FileSystem;

class Mock implements FileSystem {
  public function isFile($path) {
    return true;
  }

  public function isDir($path) {
    return true;
  }

  public function isUploadedFile($path) {
    return true;
  }

  public function moveUploadedFile($from_path, $to_path) {
    return;
  }

  public function writeToFile($path, $stream, $append = false) {
    return;
  }

  public function getInputStream() {
    return;
  }

  public function getFileStream($path) {
    return;
  }

  public function unlink($path) {
    return;
  }

  public function clearStatCache($path) {
    return;
  }

  public function getFilesize($path) {
    return 1024;
  }

}

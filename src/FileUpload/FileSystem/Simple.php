<?php

namespace FileUpload\FileSystem;

class Simple implements FileSystem {
  public function isFile($path) {
    return is_file($path);
  }

  public function isDir($path) {
    return is_dir($path);
  }

  public function isUploadedFile($path) {
    return is_uploaded_file($path);
  }

  public function moveUploadedFile($from_path, $to_path) {
    return rename($from_path, $to_path);
  }

  public function writeToFile($path, $stream, $append = false) {
    return file_put_contents($path, $stream, $append ? \FILE_APPEND : 0);
  }

  public function getInputStream() {
    return fopen('php://input', 'r');
  }

  public function getFileStream($path) {
    return fopen($path, 'r');
  }

  public function unlink($path) {
    return unlink($path);
  }

  public function clearStatCache($path) {
    return clearstatcache(true, $path);
  }

  public function getFilesize($path) {
    return filesize($path);
  }

}

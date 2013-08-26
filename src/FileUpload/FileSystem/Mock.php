<?php

namespace FileUpload\FileSystem;

class Mock implements FileSystem {
  /**
   * @see FileSystem
   */
  public function isFile($path) {
    return true;
  }

  /**
   * @see FileSystem
   */
  public function isDir($path) {
    return true;
  }

  /**
   * @see FileSystem
   */
  public function isUploadedFile($path) {
    return true;
  }

  /**
   * @see FileSystem
   */
  public function moveUploadedFile($from_path, $to_path) {
    return;
  }

  /**
   * @see FileSystem
   */
  public function writeToFile($path, $stream, $append = false) {
    return;
  }

  /**
   * @see FileSystem
   */
  public function getInputStream() {
    return;
  }

  /**
   * @see FileSystem
   */
  public function getFileStream($path) {
    return;
  }

  /**
   * @see FileSystem
   */
  public function unlink($path) {
    return;
  }

  /**
   * @see FileSystem
   */
  public function clearStatCache($path) {
    return;
  }

  /**
   * @see FileSystem
   */
  public function getFilesize($path) {
    return 1024;
  }

}

<?php

namespace FileUpload\FileSystem;

interface FileSystem {
  public function isFile($path);
  public function isDir($path);
  public function isUploadedFile($path);
  public function moveUploadedFile($from_path, $to_path);
  public function writeToFile($path, $stream, $append = false);
  public function getInputStream();
  public function getFileStream($path);
  public function unlink($path);
  public function clearStatCache($path);
  public function getFilesize($path);
}

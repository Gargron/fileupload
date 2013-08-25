<?php

namespace FileUpload;

use FileUpload\PathResolver\PathResolver;
use FileUpload\FileSystem\FileSystem;
use FileUpload\Validator\Validator;
use Psr\Log\LoggerInterface;

class FileUpload {
  /**
   * $_FILES
   * @var array
   */
  protected $upload;

  /**
   * $_SERVER
   * @var array
   */
  protected $server;

  /**
   * Path resolver instance
   * @var PathResolver
   */
  protected $pathresolver;

  /**
   * File system instance
   * @var FileSystem
   */
  protected $filesystem;

  /**
   * Optional logger
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * Validators to be run
   * @var array
   */
  protected $validators = array();

  /**
   * Construct this mother
   * @param array $upload
   * @param array $server
   */
  public function __construct($upload, $server) {
    $this->upload = isset($upload) ? $upload : null;
    $this->server = $server;
  }

  /**
   * Set path resolver
   * @param PathResolver $pr
   */
  public function setPathResolver(PathResolver $pr) {
    $this->pathresolver = $pr;
  }

  /**
   * Set file system
   * @param FileSystem $fs
   */
  public function setFileSystem(FileSystem $fs) {
    $this->filesystem = $fs;
  }

  /**
   * Set logger, optionally
   * @param LoggerInterface $logger
   */
  public function setLogger(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * Content-range header
   * @return array
   */
  protected function getContentRange() {
    return isset($this->server['HTTP_CONTENT_RANGE']) ? preg_split('/[^0-9]+/', $this->server['HTTP_CONTENT_RANGE']) : null;
  }

  /**
   * Content-length header
   * @return integer
   */
  protected function getContentLength() {
    return isset($this->server['CONTENT_LENGTH']) ? $this->server['CONTENT_LENGTH'] : null;
  }

  /**
   * Content-type header
   * @return string
   */
  protected function getContentType() {
    return isset($this->server['CONTENT_TYPE']) ? $this->server['CONTENT_TYPE'] : null;
  }

  /**
   * Request size
   * @return integer
   */
  protected function getSize() {
    $range = $this->getContentRange();
    return $range ? $range[3] : null;
  }

  /**
   * Process entire submitted request
   * @return array
   */
  public function processAll() {
    $content_range = $this->getContentRange();
    $size          = $this->getSize();
    $files         = array();
    $upload        = $this->upload;

    if($this->logger) {
      $this->logger->debug('Processing uploads', array(
        'content-range' => $content_range,
        'size'          => $size,
        'upload'        => $upload,
        'server'        => $this->server,
      ));
    }

    if($upload && is_array($upload['tmp_name'])) {
      foreach($upload['tmp_name'] as $index => $tmp_name) {
        $files[] = $this->process(
          $tmp_name,
          $upload['name'][$index],
          $size ? $size : $upload['size'][$index],
          $upload['type'][$index],
          $upload['error'][$index],
          $index,
          $content_range
        );
      }
    } else if($upload) {
      $files[] = $this->process(
        $upload['tmp_name'],
        $upload['name'],
        $size ? $size : (isset($upload['size']) ? $upload['size'] : $this->getContentLength()),
        isset($upload['type']) ? $upload['type'] : $this->getContentType(),
        $upload['error'],
        0,
        $content_range
      );
    }

    return $files;
  }

  /**
   * Ensure correct value for big integers
   * @param  integer $int
   * @return float
   */
  protected function fixIntegerOverflow($int) {
    if ($int < 0) {
      $int += 2.0 * (PHP_INT_MAX + 1);
    }

    return $int;
  }

  /**
   * Process single submitted file
   * @param  string  $tmp_name
   * @param  string  $name
   * @param  integer $size
   * @param  string  $type
   * @param  integer $error
   * @param  integer $index
   * @param  array   $content_range
   * @return File
   */
  protected function process($tmp_name, $name, $size, $type, $error, $index = 0, $content_range = null) {
    $file = new File;
    $file->name = $this->getFilename($name, $type, $index, $content_range);
    $file->size = $this->fixIntegerOverflow(intval($size));
    $file->setTypeFromPath($tmp_name);

    if($this->validate($tmp_name, $file, $error, $index)) {
      // Now that we passed the validation, we can work with the file
      $upload_path = $this->pathresolver->getUploadPath();
      $file_path   = $this->pathresolver->getUploadPath($file->name);
      $append_file = $content_range && $this->filesystem->isFile($file_path) && $file->size > $this->getFilesize($file_path);

      if($tmp_name && $this->filesystem->isUploadedFile($file_path)) {
        // This is a normal upload from temporary file
        if($append_file) {
          // Adding to existing file (chunked uploads)
          $this->filesystem->writeToFile($file_path, $this->filesystem->getFileStream($tmp_name), true);
        } else {
          // Upload full file
          $this->filesystem->moveUploadedFile($tmp_name, $file_path);
        }
      } else {
        // This is a PUT-type upload
        $this->filesystem->writeToFile($file_path, $this->filesystem->getInputStream(), $append_file);
      }

      $file_size = $this->getFilesize($file_path, $append_file);

      if($this->logger) {
        $this->logger->debug('Processing ' . $file->name, array(
          'file-path'   => $file_path,
          'file'        => $file,
          'append-file' => $append_file,
          'file-size'   => $file_size,
        ));
      }

      if($file->size == $file_size) {
        // Yay, upload is complete!
        $file->path = $file_path;
      } else {
        $file->size = $file_size;

        if(! $content_range) {
          // The file is incomplete and it's not a chunked upload, abort
          $this->filesystem->unlink($file_path);
          $file->error = 'abort';
        }
      }
    }

    return $file;
  }

  /**
   * Get filename for submitted filename
   * @param  string  $name
   * @param  string  $type
   * @param  integer $index
   * @param  array   $content_range
   * @return string
   */
  protected function getFilename($name, $type, $index, $content_range) {
    return $this->getUniqueFilename($this->trimFilename($name, $type, $index, $content_range), $type, $index, $content_range);
  }

  /**
   * Get size of file
   * @param  string  $path
   * @param  boolean $clear_cache
   * @return float
   */
  protected function getFilesize($path, $clear_cache = false) {
    if($clear_cache) {
      $this->filesystem->clearStatCache($path);
    }

    return $this->fixIntegerOverflow($this->filesystem->getFilesize($path));
  }

  /**
   * Get unique but consistent name
   * @param  string  $name
   * @param  string  $type
   * @param  integer $index
   * @param  array   $content_range
   * @return string
   */
  protected function getUniqueFilename($name, $type, $index, $content_range) {
    while($this->filesystem->isDir($this->pathresolver->getUploadPath($name))) {
      $name = $this->pathresolver->upcountName($name);
    }

    $uploaded_bytes = $this->fixIntegerOverflow(intval($content_range[1]));

    while($this->filesystem->isFile($this->pathresolver->getUploadPath($name))) {
      if($uploaded_bytes == $this->getFilesize($this->pathresolver->getUploadPath($name))) {
        break;
      }

      $name = $this->pathresolver->upcountName($name);
    }

    return $name;
  }

  /**
   * Remove harmful characters from filename
   * @param  string  $name
   * @param  string  $type
   * @param  integer $index
   * @param  array   $content_range
   * @return string
   */
  protected function trimFilename($name, $type, $index, $content_range) {
    $name = trim(basename(stripslashes($name)), ".\x00..\x20");

    if(! $name) {
      $name = str_replace('.', '-', microtime(true));
    }

    return $name;
  }

  /**
   * Convert size format from PHP config into bytes
   * @param  string $val
   * @return float
   */
  protected function getConfigBytes($val) {
    $val  = trim($val);
    $last = strtolower($val[strlen($val)-1]);

    switch($last) {
      case 'g':
        $val *= 1024;
      case 'm':
        $val *= 1024;
      case 'k':
        $val *= 1024;
    }

    return $this->fixIntegerOverflow($val);
  }

  /**
   * Validate upload
   * @param  string  $tmp_name
   * @param  File    $file
   * @param  integer $error
   * @param  integer $index
   * @return boolean
   */
  protected function validate($tmp_name, File $file, $error, $index) {
    if($error) {
      // PHP error
      $file->error = $error;
      return false;
    }

    $content_length = $this->getContentLength();
    $post_max_size  = $this->getConfigBytes(ini_get('post_max_size'));

    if($post_max_size && ($content_length > $post_max_size)) {
      // Uploaded file exceeds maximum filesize PHP accepts in the configs
      $file->error = 'Too big for PHP';
      return false;
    }

    if($tmp_name && $this->filesystem->isUploadedFile($tmp_name)) {
      $current_size = $this->getFilesize($tmp_name);
    } else {
      $current_size = $content_length;
    }

    // Now that we passed basic, implementation-agnostic tests,
    // let's do custom validators
    foreach($this->validators as $validator) {
      if(! $validator->validate($tmp_name, $file, $current_size)) {
        return false;
      }
    }

    return true;
  }

  /**
   * Add another validator
   * @param Validator $v
   */
  public function addValidator(Validator $v) {
    $this->validators[] = $v;
  }
}

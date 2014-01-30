<?php

namespace FileUpload;

use FileUpload\PathResolver\PathResolver;
use FileUpload\FileSystem\FileSystem;
use FileUpload\Validator\Validator;

class FileUploadFactory {
  /**
   * Validator to be used in the factory
   * @var Validator
   */
  protected $validator;
  
  /**
   * PathResolver to be used in the factory
   * @var PathResolver
   */
  protected $pathresolver;
  
  /**
   * FileSystem to be used in the factory
   * @var FileSystem
   */
  protected $filesystem;

  /**
   * Construct new factory with the given modules
   * @param Validator    $validator
   * @param PathResolver $pathresolver
   * @param FileSystem   $filesystem
   */
  public function __construct(Validator $validator, PathResolver $pathresolver, FileSystem $filesystem) {
    $this->validator    = $validator;
    $this->pathresolver = $pathresolver;
    $this->filesystem   = $filesystem;
  }

  /**
   * Create new instance of FileUpload with the preset modules
   * @param  array $upload
   * @param  array $server
   * @return FileUpload
   */
  public function create($upload, $server) {
    $fileupload = new FileUpload($upload, $server);
    $fileupload->setValidator($this->validator);
    $fileupload->setPathResolver($this->pathresolver);
    $fileupload->setFileSystem($this->filesystem);
    return $fileupload;
  }
}
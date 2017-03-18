<?php

namespace FileUpload;

use FileUpload\PathResolver\PathResolver;
use FileUpload\FileSystem\FileSystem;
use FileUpload\Validator\Validator;

class FileUploadFactory
{
    /**
     * Validator to be used in the factory
     * @var array
     */
    protected $validators;

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
     * @param PathResolver $pathresolver
     * @param FileSystem   $filesystem
     * @param array        $validators
     */
    public function __construct(
        PathResolver $pathresolver,
        FileSystem $filesystem,
        $validators = array()
    ) {
        $this->pathresolver = $pathresolver;
        $this->filesystem = $filesystem;
        $this->validators = $validators;
    }

    /**
     * Create new instance of FileUpload with the preset modules
     * @param  array $upload
     * @param  array $server
     * @return FileUpload
     */
    public function create($upload, $server)
    {
        $fileupload = new FileUpload($upload, $server);
        $fileupload->setPathResolver($this->pathresolver);
        $fileupload->setFileSystem($this->filesystem);

        foreach ($this->validators as $validator) {
            $fileupload->addValidator($validator);
        }

        return $fileupload;
    }
}

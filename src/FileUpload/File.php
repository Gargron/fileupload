<?php

namespace FileUpload;

class File extends \SplFileInfo
{
    /**
     * Preset no errors
     * @var mixed
     */
    public $error = 0;

    /**
     * Preset no errors
     * @var mixed
     */
    public $errorCode = 0;

    /**
     * Preset unknown mime type
     * @var string
     */
    protected $mimeType = 'application/octet-stream';

    /**
     * Is the file completely downloaded
     * @var boolean
     */
    public $completed = false;

    public function __construct($fileName)
    {
        $this->setMimeType($fileName);
        parent::__construct($fileName);
    }

    protected function setMimeType($fileName)
    {
        if (file_exists($fileName)) {
            $this->mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileName);
        }
    }

    public function getMimeType()
    {
        if ($this->getType() !== 'file') {
            throw new \Exception('You cannot get the mimetype for a ' . $this->getType());
        }

        return $this->mimeType;
    }

    /**
     * Does this file have an image mime type?
     * @return boolean
     */
    public function isImage()
    {
        return in_array(
            $this->mimeType,
            array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png')
        );
    }
}

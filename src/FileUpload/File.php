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
     * @var string
     */
    protected $clientFileName;

    /**
     * Is the file completely downloaded
     * @var boolean
     */
    public $completed = false;

    public function __construct($fileName, $clientFileName = '')
    {
        $this->setMimeType($fileName);
        $this->clientFileName = $clientFileName;
        parent::__construct($fileName);
    }

    protected function setMimeType($fileName)
    {
        if (file_exists($fileName)) {
            $this->mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileName);
        }
    }

    /**
     * Returns the "original" name of the file
     * @return string
     */
    public function getClientFileName()
    {
        return $this->clientFileName;
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
            ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png']
        );
    }
}

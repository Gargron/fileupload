<?php

namespace FileUpload;

use SplFileInfo;
use FileUpload\FileInterface;
use FileUpload\Util\MimeType;

class File extends SplFileInfo implements FileInterface
{
    protected $tmpName;

    protected $name;

    protected $extension;

    protected $mimetype;

    public function __construct(string $tmpName, string $clientUploadedFileName)
    {
        $this->tmpName = $tmpName;
        $this->name = pathinfo($clientUploadedFileName, PATHINFO_FILENAME);
        $this->extension = pathinfo($clientUploadedFileName, PATHINFO_EXTENSION);

        parent::__construct($tmpName);
    }

    public function setError(string $message, int $code)
    {
        $this->error = $message;
        $this->errorCode = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMimeType(): string
    {
        if (!$this->mimetype) {
            $this->mimetype = MimeType::fromFile($this->tmpName);
        }

        return $this->mimetype;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function isUploadedFile(): bool
    {
        return is_uploaded_file($this->getPathName());
    }

    public function isImage(): bool
    {
        // TODO(adelowo) Are this the only valid images mimetypes ?
        return in_array($this->getMimeType(), ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'], true);
    }
}

<?php

namespace FileUpload;

use FileUpload\UploadedFileInterface;

interface FileInterface extends UploadedFileInterface
{
    public function getName(): string;

    public function getExtension(): string;

    public function getMimeType(): string;
}

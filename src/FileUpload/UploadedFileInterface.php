<?php

namespace FileUpload;

interface UploadedFileInterface
{
    public function isUploadedFile(): bool;
}

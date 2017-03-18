<?php

namespace FileUpload\PathResolver;

interface PathResolver
{
    /**
     * Get absolute final destination path
     * @param  string $name
     * @return string
     */
    public function getUploadPath($name = null);

    /**
     * Ensure consistent name
     * @param  string $name
     * @return string
     */
    public function upcountName($name);
}

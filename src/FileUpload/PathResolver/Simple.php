<?php

namespace FileUpload\PathResolver;

class Simple implements PathResolver
{
    /**
     * Main path
     * @var string
     */
    protected $main_path;

    /**
     * A construct to remember
     * @param string $main_path Where files should be stored
     */
    public function __construct($main_path)
    {
        $this->main_path = $main_path;
    }

    /**
     * @see PathResolver
     */
    public function getUploadPath($name = null)
    {
        return $this->main_path . '/' . $name;
    }

    /**
     * @see PathResolver
     */
    public function upcountName($name)
    {
        return preg_replace_callback('/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/', function ($matches) {
            $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            $ext = isset($matches[2]) ? $matches[2] : '';

            return ' (' . $index . ')' . $ext;
        }, $name, 1);
    }
}

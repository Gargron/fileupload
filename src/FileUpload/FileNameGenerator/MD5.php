<?php

namespace FileUpload\FileNameGenerator;

use FileUpload\FileUpload;

class MD5 implements FileNameGenerator
{

    /**
     * Should we allow files with the same MD5'd name ?
     * @var bool
     */
    protected $allowDuplicate;

    /**
     * If $allowDuplicate is set to false - which is the default - files having the same md5'd name would be
     * overwritten. {@see Simple}
     * @param bool $allowDuplicate allows the library user determine it's behaviour
     */
    public function __construct($allowDuplicate = false)
    {
        $this->allowDuplicate = (bool)$allowDuplicate;
    }

    /**
     * Generate the md5 name of a file
     * @param  string    $source_name
     * @param  string    $type
     * @param  string    $tmp_name
     * @param  integer   $index
     * @param  string    $content_range
     * @param FileUpload $upload
     * @return bool|string if $allowDuplicate is set to false and a file with the same Md5'd name exists in the upload
     *     directory, then a bool is returned.
     */
    public function getFileName(
        $source_name,
        $type,
        $tmp_name,
        $index,
        $content_range,
        FileUpload $upload
    ) {
        $filename = pathinfo($source_name, PATHINFO_FILENAME);
        $extension = pathinfo($source_name, PATHINFO_EXTENSION);

        $md5ConcatenatedName = md5($filename) . "." . $extension;

        if ($upload->getFileSystem()
                ->doesFileExist(
                    $upload->getPathResolver()->getUploadPath($md5ConcatenatedName)
                ) &&
            $this->allowDuplicate === false
        ) {
            $upload->getFileContainer()->error = "File already exist in the upload directory. 
            Please upload another file or change it's name";

            return false;
        }

        return $md5ConcatenatedName;
    }
}

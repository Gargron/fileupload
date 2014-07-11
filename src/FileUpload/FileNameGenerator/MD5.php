<?php
/**
 * Created by PhpStorm.
 * User: decola
 * Date: 11.07.14
 * Time: 14:13
 */

namespace FileUpload\FileNameGenerator;


class MD5 implements FileNameGenerator {

    /**
     * Get file_name
     * @param  string       $source_name
     * @param  string       $type
     * @param  string       $tmp_name
     * @param  integer      $index
     * @param  string       $content_range
     * @param  Pathresolver $pathresolver
     * @param  Filesystem   $filesystem
     * @return string
     */
    public function getFileName($source_name, $type, $tmp_name, $index, $content_range, $pathresolver, $filesystem)
    {
        $filename = substr($source_name, 0, strrpos($source_name, '.'));
        $extension = substr($source_name, strrpos($source_name, '.')+1);
        return(md5($filename).".".$extension);
    }
}
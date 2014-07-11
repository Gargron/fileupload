<?php

namespace FileUpload\FileNameGenerator;

interface FileNameGenerator {

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
    public function getFileName($source_name, $type, $tmp_name, $index, $content_range, $pathresolver, $filesystem);

}

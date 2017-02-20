<?php
/**
 * Created by PhpStorm.
 * User: decola
 * Date: 11.07.14
 * Time: 14:00
 */

namespace FileUpload\FileNameGenerator;

use FileUpload\FileSystem\FileSystem;
use FileUpload\PathResolver\PathResolver;
use FileUpload\FileUpload;
use FileUpload\Util;

class Slug implements FileNameGenerator
{

    /**
     * Pathresolver
     * @var PathResolver
     */
    private $pathresolver;

    /**
     * Filesystem
     * @var FileSystem
     */
    private $filesystem;

    /**
     * Get file_name
     * @param  string       $source_name
     * @param  string       $type
     * @param  string       $tmp_name
     * @param  integer      $index
     * @param  string       $content_range
     * @param  FileUpload   $upload
     * @return string
     */
    public function getFileName($source_name, $type, $tmp_name, $index, $content_range, FileUpload $upload)
    {
        $this->filesystem   = $upload->getFileSystem();
        $this->pathresolver = $upload->getPathResolver();

        $source_name    = $this->getSluggedFileName($source_name);
        $uniqueFileName = $this->getUniqueFilename($source_name, $type, $index, $content_range);

        return $this->getSluggedFileName($uniqueFileName);
    }

    /**
     * Get unique but consistent name
     * @param  string  $name
     * @param  string  $type
     * @param  integer $index
     * @param  array   $content_range
     * @return string
     */
    protected function getUniqueFilename($name, $type, $index, $content_range)
    {
        while ($this->filesystem->isDir($this->pathresolver->getUploadPath($this->getSluggedFileName($name)))) {
            $name = $this->pathresolver->upcountName($name);
        }

        $uploaded_bytes = Util::fixIntegerOverflow(intval($content_range[1]));

        while ($this->filesystem->isFile($this->pathresolver->getUploadPath($this->getSluggedFileName($name)))) {
            if ($uploaded_bytes == $this->filesystem->getFilesize($this->pathresolver->getUploadPath($this->getSluggedFileName($name)))) {
                break;
            }

            $name = $this->pathresolver->upcountName($name);
        }

        return $name;
    }

    /**
     * @param string $name
     *
     * @return string
     * */
    public function getSluggedFileName($name)
    {
        $fileNameExploded = explode(".", $name);
        $extension        = array_pop($fileNameExploded);
        $fileNameExploded = implode(".", $fileNameExploded);

        return $this->slugify($fileNameExploded) . "." . $extension;
    }

    /**
     * @param $text
     *
     * @return mixed|string
     */
    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

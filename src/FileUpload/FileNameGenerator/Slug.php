<?php


namespace FileUpload\FileNameGenerator;


use FileUpload\FileSystem\FileSystem;
use FileUpload\FileUpload;
use FileUpload\PathResolver\PathResolver;
use FileUpload\Util;

class Slug implements FileNameGenerator {
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
	 * @param string $source_name
	 * @param string $type
	 * @param string $tmp_name
	 * @param int $index
	 * @param mixed $content_range
	 * @param FileUpload $upload
	 *
	 * @return string
	 */
	public function getFileName( $source_name, $type, $tmp_name, $index, $content_range, FileUpload $upload ) {
		$this->filesystem   = $upload->getFileSystem();
		$this->pathresolver = $upload->getPathResolver();

		$fileNameExploded = explode( ".", $source_name );
		$extension        = array_pop( $fileNameExploded );
		$fileNameExploded = implode( ".", $fileNameExploded );
		$source_name      = $this->slugify( $fileNameExploded ) . "." . $extension;

		$uniqueFileName   = $this->getUniqueFilename( $source_name, $type, $index, $content_range );
		$fileNameExploded = explode( ".", $uniqueFileName );
		$extension        = array_pop( $fileNameExploded );
		$uniqueFileName   = implode( ".", $fileNameExploded );

		return $this->slugify( $uniqueFileName ) . "." . $extension;
	}

	/**
	 * Get unique but consistent name
	 *
	 * @param  string $name
	 * @param  string $type
	 * @param  integer $index
	 * @param  mixed $content_range
	 *
	 * @return string
	 */
	protected function getUniqueFilename( $name, $type, $index, $content_range ) {
		while ( $this->filesystem->isDir( $this->pathresolver->getUploadPath( $name ) ) ) {
			$name = $this->pathresolver->upcountName( $name );
		}

		$uploaded_bytes = Util::fixIntegerOverflow( intval( $content_range[1] ) );

		while ( $this->filesystem->isFile( $this->pathresolver->getUploadPath( $name ) ) ) {
			if ( $uploaded_bytes == $this->filesystem->getFilesize( $this->pathresolver->getUploadPath( $name ) ) ) {
				break;
			}

			$name = $this->pathresolver->upcountName( $name );
		}

		return $name;
	}

	/**
	 * @param $text
	 *
	 * @return mixed|string
	 */
	protected function slugify( $text ) {
		// replace non letter or digits by -
		$text = preg_replace( '~[^\\pL\d]+~u', '-', $text );
		// trim
		$text = trim( $text, '-' );
		// transliterate
		$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );
		// lowercase
		$text = strtolower( $text );
		// remove unwanted characters
		$text = preg_replace( '~[^-\w]+~', '', $text );
		if ( empty( $text ) ) {
			return 'n-a';
		}

		return $text;
	}
}
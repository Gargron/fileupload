<?php

namespace FileUpload;

use FileUpload\FileNameGenerator\FileNameGenerator;
use FileUpload\FileNameGenerator\Simple;
use FileUpload\FileSystem\FileSystem;
use FileUpload\PathResolver\PathResolver;
use FileUpload\Validator\Validator;
use Psr\Log\LoggerInterface;

class FileUpload
{
    /**
     * Our own error constants
     */
    const UPLOAD_ERR_PHP_SIZE = 20;
    /**
     * $_FILES
     * @var array
     */
    protected $upload;
    /**
     * The array of uploaded files
     * @var array
     */
    protected $files;
    /**
     * $_SERVER
     * @var array
     */
    protected $server;
    /**
     * Path resolver instance
     * @var PathResolver
     */
    protected $pathresolver;
    /**
     * Path resolver instance
     * @var FileNameGenerator
     */
    protected $fileNameGenerator;
    /**
     * File system instance
     * @var FileSystem
     */
    protected $filesystem;
    /**
     * Optional logger
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * File Container instance
     * @var File
     */
    protected $fileContainer;
    /**
     * Validators to be run
     * @var array
     */
    protected $validators = array();
    /**
     * Callbacks to be run
     * @var array
     */
    protected $callbacks = array();
    /**
     * Default messages
     * @var array
     */
    protected $messages = array(
        // PHP $_FILES-own
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',

        // Our own
        self::UPLOAD_ERR_PHP_SIZE => 'The upload file exceeds the post_max_size or the upload_max_filesize directives in php.ini',
    );

    /**
     * Construct this mother
     * @param array             $upload
     * @param array             $server
     * @param FileNameGenerator $generator
     */
    public function __construct($upload, $server, FileNameGenerator $generator = null)
    {
        $this->upload = isset($upload) ? $upload : null;
        $this->server = $server;
        $this->fileNameGenerator = $generator ?: new Simple();
        $this->prepareMessages();
    }

    /**
     * Converts $messages array into a hash with strings as keys
     * This allows us to work with the keys and values as if it was a hash
     * Which it really should be but, well, arrays in PHP, am I right?
     */
    private function prepareMessages()
    {
        $prepared = array();

        foreach ($this->messages as $key => $msg) {
            $prepared[(string)$key] = $msg;
        }

        $this->messages = $prepared;
    }

    /**
     * @return PathResolver
     */
    public function getPathResolver()
    {
        return $this->pathresolver;
    }

    /**
     * Set path resolver
     * @param PathResolver $pr
     */
    public function setPathResolver(PathResolver $pr)
    {
        $this->pathresolver = $pr;
    }

    /**
     * @return FileNameGenerator
     */
    public function getFileNameGenerator()
    {
        return $this->fileNameGenerator;
    }

    /**
     * Set filename generator
     * @param FileNameGenerator $fng
     */
    public function setFileNameGenerator(FileNameGenerator $fng)
    {
        $this->fileNameGenerator = $fng;
    }

    /**
     * @return FileSystem
     */
    public function getFileSystem()
    {
        return $this->filesystem;
    }

    /**
     * Set file system
     * @param FileSystem $fs
     */
    public function setFileSystem(FileSystem $fs)
    {
        $this->filesystem = $fs;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set logger, optionally
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Register callback for an event
     * @param string   $event
     * @param \Closure $callback
     */
    public function addCallback($event, \Closure $callback)
    {
        $this->callbacks[$event][] = $callback;
    }

    /**
     * Merge (overwrite) default error messages
     * @param array $new_messages
     */
    public function setMessages(array $new_messages)
    {
        $this->messages = array_merge($this->messages, $new_messages);
    }

    /**
     * Returns an array of all uploaded files
     * @return array
     */
    public function getFiles()
    {
        return ($this->files);
    }

    /**
     * Process entire submitted request
     * @return array Files and response headers
     */
    public function processAll()
    {
        $content_range = $this->getContentRange();
        $size = $this->getSize();
        $this->files = array();
        $upload = $this->upload;

        if ($this->logger) {
            $this->logger->debug('Processing uploads', array(
                'Content-range' => $content_range,
                'Size' => $size,
                'Upload array' => $upload,
                'Server array' => $this->server,
            ));
        }

        if ($upload && is_array($upload['tmp_name'])) {
            foreach ($upload['tmp_name'] as $index => $tmp_name) {
                if (empty($tmp_name)) {
                    // Discard empty uploads
                    continue;
                }

                $this->files[] = $this->process(
                    $tmp_name,
                    $upload['name'][$index],
                    $size ? $size : $upload['size'][$index],
                    $upload['type'][$index],
                    $upload['error'][$index],
                    $index,
                    $content_range
                );
            }
        } else {
            if ($upload && !empty($upload['tmp_name'])) {
                $this->files[] = $this->process(
                    $upload['tmp_name'],
                    $upload['name'],
                    $size ? $size : (isset($upload['size']) ? $upload['size'] : $this->getContentLength()),
                    isset($upload['type']) ? $upload['type'] : $this->getContentType(),
                    $upload['error'],
                    0,
                    $content_range
                );
            } else {
                if ($upload && $upload['error'] != 0) {
                    // $this->fileContainer is empty at this point
                    // $upload['tmp_name'] is also empty
                    // So we create a File instance from $upload['name']
                    $file = new File($upload['name']);
                    $file->error = $this->getMessage($upload['error']);
                    $file->errorCode = $upload['error'];
                    $this->files[] = $file;
                }
            }
        }

        return array($this->files, $this->getNewHeaders($this->files, $content_range));
    }

    /**
     * Content-range header
     * @return array
     */
    protected function getContentRange()
    {
        return isset($this->server['HTTP_CONTENT_RANGE']) ?
            preg_split('/[^0-9]+/', $this->server['HTTP_CONTENT_RANGE']) : null;
    }

    /**
     * Request size
     * @return integer
     */
    protected function getSize()
    {
        $range = $this->getContentRange();

        return $range ? $range[3] : null;
    }

    /**
     * Process single submitted file
     * @param  string  $tmp_name
     * @param  string  $name
     * @param  integer $size
     * @param  string  $type
     * @param  integer $error
     * @param  integer $index
     * @param  array   $content_range
     * @return File
     */
    protected function process($tmp_name, $name, $size, $type, $error, $index = 0, $content_range = null)
    {
        $this->fileContainer = $file = new File($tmp_name);
        $file->name = $this->getFilename($name, $type, $index, $content_range, $tmp_name);
        $file->size = $this->fixIntegerOverflow(intval($size));
        $completed = false;

        if ($file->name) { //since the md5 filename generator would return false if it's allowDuplicate property is set to false and the file already exists.

            if ($this->validate($tmp_name, $file, $error, $index)) {
                // Now that we passed the validation, we can work with the file
                $upload_path = $this->pathresolver->getUploadPath();
                $file_path = $this->pathresolver->getUploadPath($file->name);
                $append_file = $content_range && $this->filesystem->isFile($file_path) && $file->size > $this->getFilesize($file_path);

                if ($tmp_name && $this->filesystem->isUploadedFile($tmp_name)) {
                    // This is a normal upload from temporary file
                    if ($append_file) {
                        // Adding to existing file (chunked uploads)
                        $this->filesystem->writeToFile($file_path, $this->filesystem->getFileStream($tmp_name), true);
                    } else {
                        // Upload full file
                        $this->filesystem->moveUploadedFile($tmp_name, $file_path);
                    }
                } else {
                    // This is a PUT-type upload
                    $this->filesystem->writeToFile($file_path, $this->filesystem->getInputStream(), $append_file);
                }

                $file_size = $this->getFilesize($file_path, $append_file);

                if ($this->logger) {
                    $this->logger->debug('Processing ' . $file->name, array(
                        'File path' => $file_path,
                        'File object' => $file,
                        'Append to file?' => $append_file,
                        'File exists?' => $this->filesystem->isFile($file_path),
                        'File size' => $file_size,
                    ));
                }

                if ($file->size == $file_size) {
                    // Yay, upload is complete!
                    $completed = true;
                } else {
                    $file->size = $file_size;

                    if (!$content_range) {
                        // The file is incomplete and it's not a chunked upload, abort
                        $this->filesystem->unlink($file_path);
                        $file->error = 'abort';
                    }
                }

                $file = new $file($file_path);
                $file->completed = $completed;
                $file->size = $file_size;

                if ($completed) {
                    $this->processCallbacksFor('completed', $file);
                }
            }
        }

        return $file;
    }

    /**
     * Get filename for submitted filename
     * @param  string  $name
     * @param  string  $type
     * @param  integer $index
     * @param  array   $content_range
     * @param  string  $tmp_name
     * @return string
     */
    protected function getFilename($name, $type, $index, $content_range, $tmp_name)
    {
        $name = $this->trimFilename($name, $type, $index, $content_range);

        return ($this->fileNameGenerator->getFileName($name, $type, $tmp_name, $index, $content_range, $this));
    }

    /**
     * Remove harmful characters from filename
     * @param  string  $name
     * @param  string  $type
     * @param  integer $index
     * @param  array   $content_range
     * @return string
     */
    protected function trimFilename($name, $type, $index, $content_range)
    {
        $name = trim(basename(stripslashes($name)), ".\x00..\x20");

        if (!$name) {
            $name = str_replace('.', '-', microtime(true));
        }

        return $name;
    }

    /**
     * Ensure correct value for big integers
     * @param  integer $int
     * @return float
     */
    protected function fixIntegerOverflow($int)
    {
        if ($int < 0) {
            $int += 2.0 * (PHP_INT_MAX + 1);
        }

        return $int;
    }

    /**
     * Validate upload using some default rules, and custom
     * validators added via addValidator. Default rules:
     *
     * - No PHP errors from $_FILES
     * - File size permitted by PHP config
     *
     * @param  string  $tmp_name
     * @param  File    $file
     * @param  integer $error
     * @param  integer $index
     * @return boolean
     */
    protected function validate($tmp_name, File $file, $error, $index)
    {
        $this->processCallbacksFor('beforeValidation', $file);

        if ($error !== 0) {
            // PHP error
            $file->error = $this->getMessage($error);
            $file->errorCode = $error;

            return false;
        }

        $content_length = $this->getContentLength();
        $post_max_size = $this->getConfigBytes(ini_get('post_max_size'));
        $upload_max_size = $this->getConfigBytes(ini_get('upload_max_filesize'));

        if (($post_max_size && ($content_length > $post_max_size)) || ($upload_max_size && ($content_length > $upload_max_size))) {
            // Uploaded file exceeds maximum filesize PHP accepts in the configs
            $file->error = $this->getMessage(self::UPLOAD_ERR_PHP_SIZE);
            $file->errorCode = self::UPLOAD_ERR_PHP_SIZE;

            return false;
        }

        if ($tmp_name && $this->filesystem->isUploadedFile($tmp_name)) {
            $current_size = $this->getFilesize($tmp_name);
        } else {
            $current_size = $content_length;
        }

        // Now that we passed basic, implementation-agnostic tests,
        // let's do custom validators
        foreach ($this->validators as $validator) {
            if (!$validator->validate($file, $current_size)) {
                return false;
            }
        }

        $this->processCallbacksFor('afterValidation', $file);

        return true;
    }

    /**
     * Process callbacks for a given event
     * @param string $eventName
     * @param File   $file
     * @return void
     */
    protected function processCallbacksFor($eventName, File $file)
    {
        if (!array_key_exists($eventName, $this->callbacks) || empty($this->callbacks[$eventName])) {
            return;
        }

        foreach ($this->callbacks[$eventName] as $callback) {
            $callback($file);
        }
    }

    /**
     * Get an error message
     * @param  int $code
     * @return string
     */
    public function getMessage($code)
    {
        return $this->messages[((string)$code)];
    }

    /**
     * Content-length header
     * @return integer
     */
    protected function getContentLength()
    {
        return isset($this->server['CONTENT_LENGTH']) ? $this->server['CONTENT_LENGTH'] : null;
    }

    /**
     * Convert size format from PHP config into bytes
     * @param  string $val
     * @return float
     */
    protected function getConfigBytes($val)
    {
        $val = trim($val);
        $bytes = (int)(substr($val, 0, -1));
        $last = strtolower($val[strlen($val) - 1]);

        switch ($last) {
            case 'g':
                $bytes *= 1024;
            case 'm':
                $bytes *= 1024;
            case 'k':
                $bytes *= 1024;
        }

        return $this->fixIntegerOverflow($bytes);
    }

    /**
     * Get size of file
     * @param  string  $path
     * @param  boolean $clear_cache
     * @return float
     */
    protected function getFilesize($path, $clear_cache = false)
    {
        if ($clear_cache) {
            $this->filesystem->clearStatCache($path);
        }

        return $this->fixIntegerOverflow($this->filesystem->getFilesize($path));
    }

    /**
     * Content-type header
     * @return string
     */
    protected function getContentType()
    {
        return isset($this->server['CONTENT_TYPE']) ? $this->server['CONTENT_TYPE'] : null;
    }

    /**
     * @return File
     */
    public function getFileContainer()
    {
        return $this->fileContainer;
    }

    /**
     * Generate headers for response
     * @param  array $files
     * @param  array $content_range
     * @return array
     */
    protected function getNewHeaders(array $files, $content_range)
    {
        $headers = array(
            'pragma' => 'no-cache',
            'cache-control' => 'no-store, no-cache, must-revalidate',
            'content-disposition' => 'inline; filename="files.json"',
            'x-content-type-options' => 'nosniff'
        );

        if ($content_range && is_object($files[0]) && isset($files[0]->size) && $files[0]->size) {
            $headers['range'] = '0-' . ($this->fixIntegerOverflow($files[0]->size) - 1);
        }

        return $headers;
    }

    /**
     * Add another validator
     * @param Validator $v
     */
    public function addValidator(Validator $v)
    {
        $this->validators[] = $v;
    }
}

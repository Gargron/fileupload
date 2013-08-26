FileUpload
==========

[![Build Status](https://travis-ci.org/Gargron/fileupload.png?branch=master)](https://travis-ci.org/Gargron/fileupload)

PHP FileUpload library that supports chunked uploads. Adopted from the
procedural script included with jQuery-File-Upload, designed to work
with that JavaScript plugin, with normal forms, and to be embeddable into
any application/architecture.

### Usage

```php
// Simple validation (max file size 2MB and only two allowed mime types)
$validator    = new FileUpload\Validator\Simple(1024 * 1024 * 2, ['image/png', 'image/jpg']);

// Simple path resolver, where uploads will be put
$pathresolver = new FileUpload\PathResolver\Simple('/my/uploads/dir');

// The machine's filesystem
$filesystem   = new FileUpload\FileSystem\Simple();

// FileUploader itself
$fileupload   = new FileUpload\FileUpload($_FILES['files'], $_SERVER);

// Adding it all together. Note that you can use multiple validators
$fileupload->setPathResolver($pathresolver);
$fileupload->setFileSystem($filesystem);
$fileupload->addValidator($validator);

// Doing the deed
list($files, $headers) = $fileupload->processAll();

// Outputting it, for example like this
foreach($headers as $header => $value) {
  header($header . ': ' . $value);
}

echo json_encode(array('files' => $files));
```

### License

Licensed under the MIT license, see `LICENSE` file.

FileUpload
==========

[![Build Status](https://travis-ci.org/Gargron/fileupload.png?branch=master)](https://travis-ci.org/Gargron/fileupload)

PHP FileUpload library that supports chunked uploads. Adopted from the
procedural script included with [jQuery-File-Upload][1], designed to work
with that JavaScript plugin, with normal forms, and to be embeddable into
any application/architecture.

[1]: https://github.com/blueimp/jQuery-File-Upload

### Installing

This package is available via Composer:

```json
{
  "require": {
    "gargron/fileupload": "~1.0.*"
  }
}
```

### Status

The unit test suite covers simple uploads, and the library "works on my machine," as it were. You are welcome to contribute.

You can grep the source code for "TODO" to find things you could help
finishing.

### Usage

```php
// Simple validation (max file size 2MB and only two allowed mime types)
$validator = new FileUpload\Validator\Simple(1024 * 1024 * 2, ['image/png', 'image/jpg']);

// Simple path resolver, where uploads will be put
$pathresolver = new FileUpload\PathResolver\Simple('/my/uploads/dir');

// The machine's filesystem
$filesystem = new FileUpload\FileSystem\Simple();

// FileUploader itself
$fileupload = new FileUpload\FileUpload($_FILES['files'], $_SERVER);

// Adding it all together. Note that you can use multiple validators or none at all
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

### Callbacks

Currently implemented events:

* `completed`

```php
$fileupload->addCallback('completed', function(FileUpload\File $file) {
  // Whoosh!
});
```

### Extending

The reason why the path resolver, the validators and the file system are
abstracted, is so you can write your own, fitting your own needs (and also,
for unit testing). The library is shipped with a bunch of "simple"
implementations which fit the basic needs. You could write a file system
implementation that works with Amazon S3, for example.

### License

Licensed under the MIT license, see `LICENSE` file.

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
    "gargron/fileupload": "~1.1.*"
  }
}
```

### Status

The unit test suite covers simple uploads, and the library "works on my machine," as it were. You are welcome to contribute.

You can grep the source code for "TODO" to find things you could help finishing.

### Usage

```php
// Simple validation (max file size 2MB and only two allowed mime types)
$validator = new FileUpload\Validator\Simple(1024 * 1024 * 2, ['image/png', 'image/jpg']);

// Alternatively, if you don't want to validate both size and type at the same time, you could use:
$mimeTypeValidator = new \FileUpload\Validator\MimeTypeValidator(["image/png", "image/jpeg"]);

// And/or (the 1st parameter is the max size while the 2nd is the min size):
$sizeValidator = new \FileUpload\Validator\SizeValidator("3M", "1M");

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

### Alternative usage via factory

```php
$factory = new FileUploadFactory(new PathResolver\Simple('/my/uploads/dir'), new FileSystem\Simple(), array(
  new \FileUpload\Validator\MimeTypeValidator(["image/png", "image/jpeg"]),
  new \FileUpload\Validator\SizeValidator("3M", "1M") // etc
));

$instance = $factory->create($_FILES['files'], $_SERVER);
```

### Validator

If you want you can use the common human readable format for filesizes like "1M", "1G", just pass the String as the first Argument.

```
$validator = new FileUpload\Validator\Simple("10M", ['image/png', 'image/jpg']);
```

Here is a listing of the possible values (B => B; KB => K; MB => M; GB => G). These values are Binary convention so basing on 1024.

### FileNameGenerator

With the FileNameGenerator you have the possibility to change the Filename the uploaded files will be saved as.

```
$fileupload = new FileUpload\FileUpload($_FILES['files'], $_SERVER);
$filenamegenerator = new FileUpload\FileNameGenerator\Simple();
$fileupload->setFileNameGenerator($filenamegenerator);
```

We have placed some example generators like md5 who saves the file under the md5 hash of the filename or the random generator witch uses an random string. The default (the simple generator to be more precise) will save the file by its origin name.

### Callbacks

Currently implemented events:

* `completed`

```php
$fileupload->addCallback('completed', function(FileUpload\File $file) {
  // Whoosh!
});
```

* `beforeValidation`

```php
$fileUploader->addCallback('beforeValidation', function (FileUpload\File $file
) {
  // About to validate the upload;
});
```

* `afterValidation`

```php
$fileUploader->addCallback('afterValidation', function (FileUpload\File $file
) {
  // Yay, we got only valid uploads
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

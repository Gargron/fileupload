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
    "gargron/fileupload": "~1.4.0"
  }
}
```

### Status

The unit test suite covers simple uploads and the library "works on my machine", as it were. You are welcome to contribute.

You can grep the source code for `TODO` to find things you could help finishing.

### Usage

```php
// Simple validation (max file size 2MB and only two allowed mime types)
$validator = new FileUpload\Validator\Simple('2M', ['image/png', 'image/jpg']);

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

echo json_encode(['files' => $files]);

foreach($files as $file){
    //Remeber to check if the upload was completed
    if ($file->completed) {
        echo $file->getRealPath();
        
        // Call any method on an SplFileInfo instance
        var_dump($file->isFile());
    }
}
```

### Alternative usage via factory

```php
$factory = new FileUploadFactory(
    new PathResolver\Simple('/my/uploads/dir'), 
    new FileSystem\Simple(), 
    [
        new FileUpload\Validator\MimeTypeValidator(['image/png', 'image/jpg']),
        new FileUpload\Validator\SizeValidator('3M', '1M') 
        // etc
    ]
);

$instance = $factory->create($_FILES['files'], $_SERVER);
```

### Validators

There are currently 4 validators shipped with `FileUpload`:

 - `Simple`
 ```php
 // Simple validation (max file size 2MB and only two allowed mime types)
 $validator = new FileUpload\Validator\Simple('2M', ['image/png', 'image/jpg']);

 ```

 - `MimeTypeValidator` 
 ```php
 $mimeTypeValidator = new FileUpload\Validator\MimeTypeValidator(['image/png', 'image/jpg']);
 ```

 - `SizeValidator`
 ```php
 // The 1st parameter is the maximum size while the 2nd is the minimum size
 $sizeValidator = new FileUpload\Validator\SizeValidator('3M', '1M');
 ```

 - `DimensionValidator`
 ```php
 $config = [
      'width' => 400,
      'height' => 500
 ]; 
 // Can also contain 'min_width', 'max_width', 'min_height' and 'max_height'

 $dimensionValidator = new FileUpload\Validator\DimensionValidator($config);
 ```

> Remember to register new validator(s) by `$fileuploadInstance->addValidator($validator);`

If you want you can use the common human readable format for filesizes like '1M', '1G', just pass the string as the first argument.

```
$validator = new FileUpload\Validator\Simple('10M', ['image/png', 'image/jpg']);
```

Here is a listing of the possible values (B => B; KB => K; MB => M; GB => G). These values are binary convention so basing on 1024.

### FileNameGenerator

With the `FileNameGenerator` you have the possibility to change the filename the uploaded files will be saved as.

```php
$fileupload = new FileUpload\FileUpload($_FILES['files'], $_SERVER);
$filenamegenerator = new FileUpload\FileNameGenerator\Simple();
$fileupload->setFileNameGenerator($filenamegenerator);
```

- `Custom`
```php
$customGenerator = new FileUpload\FileNameGenerator\Custom($provider);
//$provider can be a string (in which case it is returned as is)
//It can also be a callable or a closure which receives arguments in the other of $source_name, $type, $tmp_name, $index, $content_range, FileUpload $upload
```

- `MD5`
```php
$md5Generator = new FileUpload\FileNameGenerator\MD5($allowOverride);
//$allowOverride should be a boolean. A true value would overwrite the file if it exists while a false value would not allow the file to be uploaded since it already exists.
```

- `Random`
```php
$randomGenerator = new FileUpload\FileNameGenerator\Random($length);
//Where $length is the maximum length of the generator random name

```

- `Simple`
```php
$simpleGenerator = new FileUpload\FileNameGenerator\Simple();
//Saves a file by it's original name

```

- `Slug`
```php
$slugGenerator = new FileUpload\FileNameGenerator\Slug();
//This generator slugifies the name of the uploaded file(s)
```
> Remember to register new validator(s) by `$fileuploadInstance->setFileNameGenerator($generator);`

> Every call to `setFileNameGenerator` overrides the currently set `$generator`

### Callbacks

Currently implemented events:

- `completed`
```php
$fileupload->addCallback('completed', function(FileUpload\File $file) {
    // Whoosh!
});
```

- `beforeValidation`
```php
$fileUploader->addCallback('beforeValidation', function (FileUpload\File $file) {
    // About to validate the upload;
});
```

- `afterValidation`
```php
$fileUploader->addCallback('afterValidation', function (FileUpload\File $file) {
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

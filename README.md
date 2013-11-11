Image Manager
=============

Installation to project
-----------------------
The best way to install h4kuna/image-manager is using Composer:
```sh
$ composer require h4kuna/image-manager:@dev
```

Example NEON config
-------------------
<pre>
extensions:
    imageExtension: h4kuna\DI\ImageExtension

imageExtension:
    namespace:
        small: {'80x80'} # NULL size is original
        big: {'200x200', 'fit'}

    # Following properties are not required
    maxSize: 1500x1500
    domain: http://example.com
    test: TRUE
    noImage: 'relative path from source to image'
    temp: 'relative path from wwwDir'
    source: 'relative path from wwwDir'
    wwwDir: '%wwwDir%'


</pre>

## Usage

### Saving images

```php
/** @var h4kuna\ImageManager $imageManager */
$imageManager->saveNetteUpload($fileUpload); // saves to .../sourceDir/filename.jpg
$imageManager->saveNetteUpload($fileUpload, 'my-path'); // saves to .../sourceDir/my-path/filename.jpg
// look at $imageManager->save*
```

### Remove images
```php
$imageManager->unlink('filename.jpg'); // search in all namespaces and remove from temp
```

### Using in Latte

```html
<a href="{img small/filename.jpg}">
```

You can write params inline to macro, above is recommended for unlink method.
```html
<a href="{img big/filename.jpg}"><img n:img="small/filename.jpg"></a>
```

output:

```html
<a href="/sourceDir/temp/big/filename.jpg"><img n:img="/sourceDir/temp/small/filename.jpg"></a>
```

### Resizing flags

For resizing (third argument) you can use these keywords - `fit`, `fill`, `exact`, `stretch`, `shrink_only`. For details see comments above [these constants](http://api.nette.org/2.0/source-common.Image.php.html#105)



Run simple example
------------------
```sh
$ cd to/your/web/document/root
$ git clone git@github.com:h4kuna/image-manager.git
$ cd image-manager
$ chmod 777 tests/tmp
$ composer install
```

Look at to example.php and read it, after you can open in browser and you can see how it is work.

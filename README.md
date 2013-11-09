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
    namespace: {'small': {'80x80'}} # size is required
    # name: {'big': {size, method, path, quality}, ...}

    # Following properties are not required
    maxSize: 1500x1500
    domain: http://example.com
    test: TRUE
    noImage: 'relative path from source to image'
    temp: 'relative path from source'
    source: 'relative path from source'
    wwwDir: '%wwwDir%'


</pre>

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

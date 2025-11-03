# remote-thumbnail-cache-wrapper

generate & cache thumbnails of remote & local images

## Requirements

- mininum php version 8.4
- curl extension must be enabled
- gd extension must be enabled

## Limitations

At this time only JPEG & PNG formats are supported.

## Install (composer) dependencies:

```Shell
composer require aportela/remote-thumbnail-cache-wrapper
```

## Code example (from remote picture):

```php
<?php

    require "vendor/autoload.php";

    $logger = new \Psr\Log\NullLogger("");

    $cachePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "cache";

    $url = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/refs/heads/main/src/Test/200.jpg";
    $source = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource($url);

    // JPEG, quality: 80, resolution: 320x200
    $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail($logger, $cachePath, $source, 80, 320, 200);
    // get thumbnail local path (from cache || create cache if not found)
    $path = $thumbnail->get();
    if ($path !== false) {
        header("Content-Type: image/jpeg");
        readfile($path);
    } else {
        header("HTTP/1.1 404 Not Found");
    }
```

# Code example (from local filesystem picture):

```php
<?php

    require "vendor/autoload.php";

    $logger = new \Psr\Log\NullLogger("");

    $cachePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "cache";

    $localImagePath = "/tmp/existent_image.jpg";
    $source = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource($localImagePath);

    // PNG, quality: 90, resolution: 160x100
    $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail($logger, $cachePath, $source, 80, 160, 100);
    // get thumbnail local path (from cache || create cache if not found)
    $path = $thumbnail->get();
    if ($path !== false) {
        header("Content-Type: image/png");
        readfile($path);
    } else {
        header("HTTP/1.1 404 Not Found");
    }
```

![PHP Composer](https://github.com/aportela/remote-thumbnail-cache-wrapper/actions/workflows/php.yml/badge.svg)

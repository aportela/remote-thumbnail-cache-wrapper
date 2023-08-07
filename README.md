# remote-thumbnail-cache-wrapper

generate & cache thumbnails of remote & local images

## Requirements

- mininum php version 8.x
- curl extension must be enabled
- gd extension must be enabled

## Limitations

At this time only JPEG format is supported.

## Install (composer) dependencies:

```
composer require aportela/remote-thumbnail-cache-wrapper
```

## Code example (from remote picture):

```
<?php

    require "vendor/autoload.php";

    $logger = new \Psr\Log\NullLogger("");

    // cached thumbnails will be stored on this path
    $localPath = "./data/";

    $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail($logger, $localPath);
    $thumbnail->setDimensions(250, 250);
    $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);

    if ($thumbnail->getFromRemoteURL("https://i.imgur.com/1bo3VaU.jpeg")) {
        header("Content-Type: image/jpeg");
        readfile($thumbnail->path);
    } else {
        header("HTTP/1.1 404 Not Found");
    }
```

# Code example (from local filesystem picture):

```
<?php

    require "vendor/autoload.php";

    $logger = new \Psr\Log\NullLogger("");

    // cached thumbnails will be stored on this path
    $localPath = "./data/";

    $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail($logger, $localPath);
    $thumbnail->setDimensions(250, 250);
    $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);

    if ($thumbnail->getFromLocalFilesystem("/tmp/test.jpg")) {
        header("Content-Type: image/jpeg");
        readfile($thumbnail->path);
    } else {
        header("HTTP/1.1 404 Not Found");
    }
```

![PHP Composer](https://github.com/aportela/remote-thumbnail-cache-wrapper/actions/workflows/php.yml/badge.svg)

# remote-thumbnail-cache-wrapper
 generate & cache thumbnails of remote images

## Install (composer) dependencies:

```
composer require aportela/remote-thumbnail-cache-wrapper
composer require monolog/monolog
```

# Code example (from remote picture):

```
<?php

    require "vendor/autoload.php";

    $logger = new \Monolog\Logger("remote-thumbnail-cache-wrapper");

    // cached thumbnails will be stored on this path
    $localPath = "./data/";

    $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\Thumbnail($logger, $localPath);

    if ($thumbnail->getFromRemoteURL("https://i.imgur.com/1bo3VaU.jpeg", 250, 250)) {
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

    $logger = new \Monolog\Logger("remote-thumbnail-cache-wrapper");

    // cached thumbnails will be stored on this path
    $localPath = "./data/";

    $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\Thumbnail($logger, $localPath);

    if ($thumbnail->getFromLocalFilesystem("/tmp/test.jpg", 250, 250)) {
        header("Content-Type: image/jpeg");
        readfile($thumbnail->path);
    } else {
        header("HTTP/1.1 404 Not Found");
    }
```
# remote-thumbnail-cache-wrapper
 generate & cache thumbnails of remote images

## Install (composer) dependencies:

```
composer require aportela/remote-thumbnail-cache-wrapper
composer require monolog/monolog
```

# Code example:

```
<?php

    require "vendor/autoload.php";

    $logger = new \Monolog\Logger("remote-thumbnail-cache-wrapper");

    // cached thumbnails will be stored on this path
    $localPath = "./data/";

    $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\Thumbnail($logger, $localPath);

    header("Content-Type: image/jpeg");

    $thumbnail->getFromRemoteURL("https://i.imgur.com/1bo3VaU.jpeg", 250, 250);

```
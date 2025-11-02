<?php

namespace aportela\RemoteThumbnailCacheWrapper;

final class JPEGThumbnail extends \aportela\RemoteThumbnailCacheWrapper\Thumbnail
{
    public const DEFAULT_IMAGE_QUALITY = 95;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, \aportela\RemoteThumbnailCacheWrapper\Source\ISource $source, int $quality = self::DEFAULT_IMAGE_QUALITY, int $width = self::DEFAULT_THUMBNAIL_WIDTH, int $height = self::DEFAULT_THUMBNAIL_HEIGHT)
    {
        parent::__construct($logger, $localBasePath, $source, \aportela\RemoteThumbnailCacheWrapper\ThumbnailType::JPG, $quality, $width, $height);
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}

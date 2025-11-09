<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper;

use PHPImageWorkshop\ImageWorkshop;

final class PNGThumbnail extends \aportela\RemoteThumbnailCacheWrapper\Thumbnail
{
    public const int DEFAULT_IMAGE_QUALITY = 95;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, \aportela\RemoteThumbnailCacheWrapper\Source\ISource $source, int $quality = self::DEFAULT_IMAGE_QUALITY, int $width = self::DEFAULT_THUMBNAIL_WIDTH, int $height = self::DEFAULT_THUMBNAIL_HEIGHT)
    {
        parent::__construct($logger, $localBasePath, $source, \aportela\RemoteThumbnailCacheWrapper\ThumbnailType::PNG, $quality, $width, $height);
    }
}

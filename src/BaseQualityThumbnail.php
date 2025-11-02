<?php

namespace aportela\RemoteThumbnailCacheWrapper;

use PHPImageWorkshop\ImageWorkshop;

abstract class BaseQualityThumbnail extends \aportela\RemoteThumbnailCacheWrapper\BaseThumbnail
{
    protected int $quality;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, int $quality, ?int $width = null, ?int $height = null)
    {
        parent::__construct($logger, $localBasePath, $width, $height);
        $this->setQuality($quality);
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function setQuality(int $quality): void
    {
        if ($quality >= 0 && $quality <= 100) {
            $this->quality = $quality;
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::setQuality - Invalid quality value", [$quality]);
            throw new \InvalidArgumentException("Invalid quality value: " . $quality);
        }
    }
}

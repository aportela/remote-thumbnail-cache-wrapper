<?php

namespace aportela\RemoteThumbnailCacheWrapper;

abstract class Thumbnail implements \aportela\RemoteThumbnailCacheWrapper\IThumbnail
{
    protected \Psr\Log\LoggerInterface $logger;
    protected string $localBasePath;
    protected int $width;
    protected int $height;
    protected int $quality;

    protected \aportela\SimpleFSCache\Cache $cache;
    public ?string $path = null;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, int $quality, ?int $width = null, ?int $height = null)
    {
        $this->logger = $logger;
        $this->cache = new \aportela\SimpleFSCache\Cache($logger, \aportela\SimpleFSCache\CacheFormat::NONE, $localBasePath, false);
        $this->localBasePath = $localBasePath;
        if ($width != null && $height != null) {
            $this->setDimensions($width, $height);
        }
        $this->setQuality($quality);
    }

    public function __destruct() {}

    public function setDimensions(int $width, int $height): void
    {
        if ($width > 0 && $height > 0) {
            $this->width = $width;
            $this->height = $height;
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\BaseThumbnail::setDimensions - Invalid dimension values", [$width, $height]);
            throw new \InvalidArgumentException("Invalid dimension values: " . print_r([$width, $height], true));
        }
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

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

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, int $quality, int $width, int $height)
    {
        $this->logger = $logger;
        $this->cache = new \aportela\SimpleFSCache\Cache($logger, \aportela\SimpleFSCache\CacheFormat::NONE, $localBasePath, false);
        if ($this->cache->isEnabled()) {
            $this->setDimensions($width, $height, false);
            $this->setQuality($quality, false);
            // save original cache base path
            $this->localBasePath = $this->cache->getBasePath();
            $this->refreshCacheBasePath();
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::__construct - Error setting cache", [$localBasePath]);
            throw new \RuntimeException("Error setting cache, path:" . $localBasePath);
        }
    }

    public function __destruct() {}

    protected function getThumbnailBasePath(): string
    {
        return (
            sprintf(
                "%s%s%dx%d-%03d",
                $this->localBasePath,
                DIRECTORY_SEPARATOR,
                $this->width,
                $this->height,
                $this->quality
            )
        );
    }

    protected function refreshCacheBasePath()
    {
        $this->cache->setBasePath($this->getThumbnailBasePath());
    }

    public function setDimensions(int $width, int $height, bool $refreshCacheBasePath = true): void
    {
        if ($width > 0 && $height > 0) {
            $this->width = $width;
            $this->height = $height;
            if ($refreshCacheBasePath) {
                $this->refreshCacheBasePath();
            }
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::setDimensions - Invalid dimension values", [$width, $height]);
            throw new \InvalidArgumentException("Invalid dimension values: " . print_r([$width, $height], true));
        }
    }

    public function setQuality(int $quality, bool $refreshCacheBasePath = true): void
    {
        if ($quality >= 0 && $quality <= 100) {
            $this->quality = $quality;
            if ($refreshCacheBasePath) {
                $this->refreshCacheBasePath();
            }
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::setQuality - Invalid quality value", [$quality]);
            throw new \InvalidArgumentException("Invalid quality value: " . $quality);
        }
    }

    protected function getThumbnailDirectory(string $hash): string
    {
        return (
            sprintf(
                "",
                $this->getThumbnailBasePath(),
                DIRECTORY_SEPARATOR,
                implode(
                    DIRECTORY_SEPARATOR,
                    [
                        substr($hash, 0, 1),
                        substr($hash, 1, 1),
                        substr($hash, 2, 1),
                        substr($hash, 3, 1)
                    ]
                )
            )
        );
    }

    protected function getThumbnailFullPath(string $hash, string $extension): string
    {
        return ($this->getThumbnailDirectory($hash) . DIRECTORY_SEPARATOR . "$hash.{$extension}");
    }
}

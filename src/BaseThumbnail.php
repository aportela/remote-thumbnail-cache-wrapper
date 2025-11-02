<?php

namespace aportela\RemoteThumbnailCacheWrapper;

abstract class BaseThumbnail implements \aportela\RemoteThumbnailCacheWrapper\IThumbnail
{
    protected \Psr\Log\LoggerInterface $logger;
    protected string $localBasePath;
    protected int $width;
    protected int $height;
    protected \aportela\SimpleFSCache\Cache $cache;
    public ?string $path = null;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath)
    {
        $this->logger = $logger;
        $tmpPath = realpath($localBasePath);
        if ($tmpPath !== false) {
            if (!file_exists($tmpPath)) {
                $this->logger->info("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::__construct - Creating missing path: {$localBasePath}");
                if (! mkdir($tmpPath, 0750)) {
                    $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::__construct - Error creating missing path: {$localBasePath}");
                    throw new \aportela\RemoteThumbnailCacheWrapper\Exception\FileSystemException("");
                }
                $this->localBasePath = $tmpPath;
            }
            $this->cache = new \aportela\SimpleFSCache\Cache($this->logger, \aportela\SimpleFSCache\CacheFormat::NONE, $this->localBasePath, false);
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::__construct - Invalid localBasePath param value", [$localBasePath]);
            throw new \aportela\RemoteThumbnailCacheWrapper\Exception\FileSystemException("");
        }
    }

    public function __destruct() {}

    public function setDimensions(int $width, int $height): void
    {
        $this->width = $width;
        $this->height = $height;
    }
}

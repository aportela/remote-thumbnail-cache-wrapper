<?php

namespace aportela\RemoteThumbnailCacheWrapper;

abstract class Thumbnail implements \aportela\RemoteThumbnailCacheWrapper\iThumbnail
{
    protected \Psr\Log\LoggerInterface $logger;
    protected string $localBasePath;
    protected int $width;
    protected int $height;
    public ?string $path = null;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath)
    {
        $this->logger = $logger;
        $this->localBasePath = realpath($localBasePath);
        if (!file_exists($this->localBasePath)) {
            mkdir($this->localBasePath, 0750);
        }
        $this->logger->debug("RemoteThumbnailCacheWrapper::__construct");
    }

    public function __destruct()
    {
        $this->logger->debug("RemoteThumbnailCacheWrapper::__destruct");
    }

    public function setDimensions(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
}

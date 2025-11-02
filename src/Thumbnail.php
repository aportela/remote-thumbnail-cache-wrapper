<?php

namespace aportela\RemoteThumbnailCacheWrapper;

use PHPImageWorkshop\ImageWorkshop;

abstract class Thumbnail
{
    protected \Psr\Log\LoggerInterface $logger;
    protected string $localBasePath;
    protected int $width;
    protected int $height;
    protected int $quality;

    protected \aportela\SimpleFSCache\Cache $cache;
    public ?string $path = null;

    protected string $hash;

    protected \aportela\RemoteThumbnailCacheWrapper\Source\ISource $source;

    protected \aportela\RemoteThumbnailCacheWrapper\ThumbnailType $type;

    public const DEFAULT_THUMBNAIL_WIDTH = 320;
    public const DEFAULT_THUMBNAIL_HEIGHT = 200;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, \aportela\RemoteThumbnailCacheWrapper\Source\ISource $source, \aportela\RemoteThumbnailCacheWrapper\ThumbnailType $type, int $quality, int $width, int $height)
    {
        $this->logger = $logger;
        $this->cache = new \aportela\SimpleFSCache\Cache($logger, \aportela\SimpleFSCache\CacheFormat::NONE, $localBasePath, false);
        if ($this->cache->isEnabled()) {
            // save original cache base path
            $this->localBasePath = $this->cache->getBasePath();
            $this->source = $source;
            $this->hash = $source->getHash();
            $this->type = $type;
            $this->setQuality($quality, false);
            $this->setDimensions($width, $height, false);
            // set new cache base path (append widthxheight-quality)
            $this->refreshCacheBasePath();
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::__construct - Error setting cache", [$localBasePath]);
            throw new \RuntimeException("Error setting cache, path:" . $localBasePath);
        }
    }

    public function __destruct()
    {
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

    protected function getThumbnailDirectory(): string
    {
        return (
            sprintf(
                "",
                $this->getThumbnailBasePath(),
                DIRECTORY_SEPARATOR,
                implode(
                    DIRECTORY_SEPARATOR,
                    [
                        substr($this->hash, 0, 1),
                        substr($this->hash, 1, 1),
                        substr($this->hash, 2, 1),
                        substr($this->hash, 3, 1)
                    ]
                )
            )
        );
    }

    protected function getThumbnailFullPath(): string
    {
        return ($this->getThumbnailDirectory($this->hash) . DIRECTORY_SEPARATOR .  "{$this->hash}.{$this->type->value}");
    }

    protected function isCached(): bool
    {
        return (file_exists($this->getThumbnailFullPath()));
    }

    protected function create(string $sourcePath): bool
    {
        if (file_exists(($sourcePath))) {
            $thumb = ImageWorkshop::initFromPath($sourcePath);
            if ($thumb->getWidth() > $this->width) {
                $thumb->resizeInPixel($this->width, null, true);
            }
            $this->logger->debug("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::createThumbnail (width: {$this->width} / height: {$this->height} / quality: {$this->quality})");
            $destPath = $this->getThumbnailFullPath();
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            $thumb->save(dirname($destPath), basename($destPath), true, null, $this->quality);
            return (true);
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::createThumbnail - Error: source path ({$sourcePath})not found");
            return (false);
        }
    }

    protected function saveRemoteURLIntoTemporalFile(string $url): bool|string
    {
        $http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger);
        $response = $http->GET($url);
        if ($response->code == 200) {
            if (in_array($response->getContentType(), ["image/jpeg", "image/png"])) {
                $tmpFile = tempnam(sys_get_temp_dir(), uniqid());
                file_put_contents($tmpFile, $response->body);
                return ($tmpFile);
            } else {
                $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::saveRemoteURLIntoTemporalFile - Invalid/unsupported mime type", [$response->getContentType()]);
                return (false);
            }
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::saveRemoteURLIntoTemporalFile - Invalid response code", [$url, $response->code]);
            return (false);
        }
    }

    public function getFromRemoteURL(string $url, bool $force = false): bool|string
    {
        $localFilePath = $this->getThumbnailFullPath();
        if ($force && file_exists($localFilePath)) {
            unlink($localFilePath);
        }
        if (! $this->isCached()) {
            $temporalPath = $this->saveRemoteURLIntoTemporalFile($url);
            if ($temporalPath !== false) {
                $this->create($temporalPath);
                unlink($temporalPath);
                return ($localFilePath);
            } else {
                return (false);
            }
        } else {
            return ($localFilePath);
        }
    }

    public function getFromLocalFilesystem(string $path, bool $force = false): bool|string
    {
        $localFilePath = $this->getThumbnailFullPath();
        if ($force && file_exists($localFilePath)) {
            unlink($localFilePath);
        }
        if (! $this->isCached()) {
            if (file_exists($path)) {
                $this->create($path);
                return ($localFilePath);
            } else {
                return (false);
            }
        } else {
            return ($localFilePath);
        }
    }
}

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

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, \aportela\RemoteThumbnailCacheWrapper\Source\ISource $source, \aportela\RemoteThumbnailCacheWrapper\ThumbnailType $type, int $quality, int $width, int $height, null|int|\DateInterval $ttl = null)
    {
        $this->logger = $logger;
        switch ($type) {
            case \aportela\RemoteThumbnailCacheWrapper\ThumbnailType::JPG:
                $this->cache = new \aportela\SimpleFSCache\Cache($logger, $localBasePath, $ttl, \aportela\SimpleFSCache\CacheFormat::JPG);
                break;
            case \aportela\RemoteThumbnailCacheWrapper\ThumbnailType::PNG:
                $this->cache = new \aportela\SimpleFSCache\Cache($logger, $localBasePath, $ttl, \aportela\SimpleFSCache\CacheFormat::PNG);
                break;
            default:
                $this->cache = new \aportela\SimpleFSCache\Cache($logger, $localBasePath, $ttl, \aportela\SimpleFSCache\CacheFormat::NONE);
                break;
        }

        // save original cache base path
        $this->localBasePath = (string) $this->cache->getBasePath();
        $this->source = $source;
        $this->hash = $source->getHash();
        $this->type = $type;
        $this->setQuality($quality, false);
        $this->setDimensions($width, $height, false);
        // set new cache base path (append /widthxheight-quality/)
        $this->refreshCacheBasePath();
    }

    public function __destruct()
    {
    }

    private function getThumbnailBasePath(): string
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

    protected function refreshCacheBasePath(): void
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
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::setDimensions - Invalid dimensions", [$width, $height]);
            throw new \aportela\RemoteThumbnailCacheWrapper\Exception\InvalidDimensionsException("Invalid dimensions: " . print_r([$width, $height], true));
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
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::setQuality - Invalid quality", [$quality]);
            throw new \aportela\RemoteThumbnailCacheWrapper\Exception\InvalidQualityException("Invalid quality: " . $quality);
        }
    }

    private function getThumbnailFullPath(): string
    {
        return ($this->cache->getCacheKeyFilePath($this->hash));
    }

    public function isCached(): bool
    {
        return ($this->cache->has($this->hash));
    }

    public function deleteCache(): bool
    {
        return ($this->cache->delete($this->hash));
    }

    private function create(string $sourcePath): bool
    {
        if (file_exists(($sourcePath))) {
            $thumb = ImageWorkshop::initFromPath($sourcePath);
            if ($thumb->getWidth() > $this->width) {
                $thumb->resizeInPixel($this->width, null, true);
            }
            $destPath = $this->getThumbnailFullPath();
            if (file_exists($destPath)) {
                $this->logger->info("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::create - Removing existent file", [$destPath]);
                if (! unlink($destPath)) {
                    $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::create - Error removing file", [$destPath]);
                    throw new \aportela\RemoteThumbnailCacheWrapper\Exception\FileSystemException("Error removing file " . $destPath);
                }
            }
            $thumb->save(dirname($destPath), basename($destPath), true, null, $this->quality);
            return (true);
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::create - Error: source path ({$sourcePath}) not found");
            return (false);
        }
    }

    private function saveRemoteURLIntoTemporalFile(string $url): bool|string
    {
        $http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger);
        $response = $http->GET($url);
        if ($response->code == 200) {
            if (in_array($response->getContentType(), ["image/jpeg", "image/png"])) {
                $tmpFile = tempnam(sys_get_temp_dir(), uniqid());
                if (is_string($tmpFile)) {
                    if (file_put_contents($tmpFile, $response->body) !== false) {
                        ;
                        return ($tmpFile);
                    } else {
                        $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::saveRemoteURLIntoTemporalFile - Error saving contents into temporal file");
                        return (false);
                    }
                } else {
                    $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::saveRemoteURLIntoTemporalFile - Error creating temporal file");
                    return (false);
                }
            } else {
                $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::saveRemoteURLIntoTemporalFile - Invalid/unsupported mime type", [$response->getContentType()]);
                return (false);
            }
        } else {
            $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::saveRemoteURLIntoTemporalFile - Invalid http response code", [$url, $response->code]);
            return (false);
        }
    }

    private function getFromRemoteURL(string $url, bool $force = false): bool|string
    {
        $thumbnailPath = $this->getThumbnailFullPath();
        if ($force && $this->isCached()) {
            $this->deleteCache();
        }
        if (! $this->isCached()) {
            $temporalPath = $this->saveRemoteURLIntoTemporalFile($url);
            if (is_string($temporalPath)) {
                if ($this->create($temporalPath)) {
                    if (! unlink($temporalPath)) {
                        $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::getFromRemoteURL - Error removing temporal file", [$temporalPath]);
                    }
                    return ($thumbnailPath);
                } else {
                    $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::getFromRemoteURL - Error creating thumbnail from temporal file", [$temporalPath]);
                    if (! unlink($temporalPath)) {
                        $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::getFromRemoteURL - Error removing temporal file", [$temporalPath]);
                    }
                    return (false);
                }
            } else {
                $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::getFromRemoteURL - Error saving remote url into temporal file", [$url]);
                return (false);
            }
        } else {
            return ($thumbnailPath);
        }
    }

    private function getFromLocalFilesystem(string $path, bool $force = false): bool|string
    {
        $thumbnailPath = $this->getThumbnailFullPath();
        if ($force && $this->isCached()) {
            $this->deleteCache();
        }
        if (! $this->isCached()) {
            if (file_exists($path)) {
                if ($this->create($path)) {
                    return ($thumbnailPath);
                } else {
                    $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::getFromRemoteURL - Error creating thumbnail from local path", [$path]);
                    return (false);
                }
            } else {
                $this->logger->error("\aportela\RemoteThumbnailCacheWrapper\Thumbnail::getFromLocalFilesystem - Error: local path not found", [$path]);
                return (false);
            }
        } else {
            return ($thumbnailPath);
        }
    }

    public function get(bool $force = false): bool|string
    {
        switch (get_class($this->source)) {
            case "aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource":
                return ($this->getFromLocalFilesystem($this->source->getResource(), $force));
            case "aportela\RemoteThumbnailCacheWrapper\Source\URLSource":
                return ($this->getFromRemoteURL($this->source->getResource(), $force));
            default:
                return (false);
        }
    }
}

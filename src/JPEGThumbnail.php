<?php

namespace aportela\RemoteThumbnailCacheWrapper;

use PHPImageWorkshop\ImageWorkshop;

final class JPEGThumbnail extends \aportela\RemoteThumbnailCacheWrapper\BaseThumbnail
{
    private const OUTPUT_FORMAT_EXTENSION = "jpg";
    public const DEFAULT_IMAGE_QUALITY = 95;

    private int $quality;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath)
    {
        $this->logger = $logger;
        $this->localBasePath = realpath($localBasePath);
        if (!file_exists($this->localBasePath)) {
            mkdir($this->localBasePath, 0750);
        }
        $this->quality = self::DEFAULT_IMAGE_QUALITY;
        $this->logger->debug("RemoteThumbnailCacheWrapper::__construct");
    }

    public function __destruct()
    {
        $this->logger->debug("RemoteThumbnailCacheWrapper::__destruct");
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

    private function getThumbnailLocalPath(int $width, int $height, int $quality, string $hash): string
    {
        return (sprintf("%s%s%s.%s", implode(DIRECTORY_SEPARATOR, [$this->localBasePath, $quality, $width, $height, substr($hash, 0, 1), substr($hash, 1, 1)]), DIRECTORY_SEPARATOR, $hash, self::OUTPUT_FORMAT_EXTENSION));
    }

    public function remoteURLExistsInCache(string $url): bool
    {
        if ($this->width > 0 && $this->height > 0 && $this->quality > 0) {
            return (file_exists($this->getThumbnailLocalPath($this->width, $this->height, $this->quality, sha1($url))));
        } else {
            return (false);
        }
    }

    public function localFilesystemExistsInCache(string $path): bool
    {
        if ($this->width > 0 && $this->height > 0 && $this->quality > 0) {
            return (file_exists($this->getThumbnailLocalPath($this->width, $this->height, $this->quality, sha1($path))));
        } else {
            return (false);
        }
    }

    private function createThumbnail(string $sourcePath, int $width, int $height, int $jpegImageQuality, string $hash): void
    {
        if (file_exists(($sourcePath))) {
            $thumb = ImageWorkshop::initFromPath($sourcePath);
            if ($thumb->getWidth() > $width) {
                $thumb->resizeInPixel($width, null, true);
            }
            $this->logger->debug("RemoteThumbnailCacheWrapper::createThumbnail (width: " . $width . " / height: " . $height . " / quality: " . $jpegImageQuality . ")");
            $destPath = $this->getThumbnailLocalPath($width, $height, $jpegImageQuality, $hash);
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            $thumb->save(dirname($destPath), basename($destPath), true, null, $jpegImageQuality);
        } else {
            $this->logger->error("RemoteThumbnailCacheWrapper::createThumbnail source path not found: " . $sourcePath);
        }
    }

    public function getFromRemoteURL(string $url, bool $force = false): bool
    {
        $hash = sha1($url);
        $localFilePath = $this->getThumbnailLocalPath($this->width, $this->height, $this->quality, $hash);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromRemoteURL - localPath => " . $localFilePath);
        if ($force && file_exists($localFilePath)) {
            $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - removing current thumbnail => " . $localFilePath);
            unlink($localFilePath);
        }
        if (!file_exists($localFilePath)) {
            $this->logger->debug("RemoteThumbnailCacheWrapper::getFromRemoteURL - local thumbnail not found... creating...");
            $http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger);
            $response = $http->GET($url);
            if ($response->code == 200) {
                if (in_array($response->getContentType(), ["image/jpeg", "image/png"])) {
                    $tmpFile = tempnam(sys_get_temp_dir(), "axl");
                    file_put_contents($tmpFile, $response->body);
                    $this->createThumbnail($tmpFile, $this->width, $this->height, $this->quality, $hash);
                    unlink($tmpFile);
                    $this->path = $localFilePath;
                    return (true);
                } else {
                    return (false);
                }
            } else {
                $this->logger->critical("RemoteThumbnailCacheWrapper::getFromRemoteURL ERROR: Invalid remote HTTP code: " . $response->code);
                return (false);
            }
        } else {
            $this->path = $localFilePath;
            return (true);
        }
    }

    public function getFromLocalFilesystem(string $path, bool $force = false): bool
    {
        $hash = sha1($path);
        $localFilePath = $this->getThumbnailLocalPath($this->width, $this->height, $this->quality, $hash);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - localPath => " . $localFilePath);
        if ($force && file_exists($localFilePath)) {
            $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - removing current thumbnail => " . $localFilePath);
            unlink($localFilePath);
        }
        if (!file_exists($localFilePath)) {
            if (file_exists($path)) {
                $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - local thumbnail not found... creating...");
                $this->createThumbnail($path, $this->width, $this->height, $this->quality, $hash);
                $this->path = $localFilePath;
                return (true);
            } else {
                $this->logger->critical("RemoteThumbnailCacheWrapper::getFromLocalFilesystem ERROR: path not found: " . $path);
                return (false);
            }
        } else {
            $this->path = $localFilePath;
            return (true);
        }
    }

    public function getFromCache(string $hash): bool
    {
        $localFilePath = $this->getThumbnailLocalPath($this->width, $this->height, $this->quality, $hash);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromCache - localPath => " . $localFilePath);
        if (file_exists($localFilePath)) {
            $this->path = $localFilePath;
            return (true);
        } else {
            $this->logger->critical("RemoteThumbnailCacheWrapper::getFromLocalFilesystem ERROR: path not found: " . $localFilePath);
            return (false);
        }
    }
}

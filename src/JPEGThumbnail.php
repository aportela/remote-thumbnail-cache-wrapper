<?php

namespace aportela\RemoteThumbnailCacheWrapper;

use \PHPImageWorkshop\ImageWorkshop;

class JPEGThumbnail
{

    protected const OUTPUT_FORMAT_EXTENSION = "jpg";
    public const DEFAULT_JPEG_IMAGE_QUALITY = 95;

    protected \Psr\Log\LoggerInterface $logger;
    private string $localBasePath;
    private string $hash;
    public $path;

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

    private function getThumbnailLocalPath(int $width, int $height, int $quality): string
    {
        return (sprintf("%s%s%s.%s", implode(DIRECTORY_SEPARATOR, [$this->localBasePath, $quality, $width, $height, substr($this->hash, 0, 1), substr($this->hash, 1, 1)]), DIRECTORY_SEPARATOR, $this->hash, self::OUTPUT_FORMAT_EXTENSION));
    }

    private function createThumbnail($sourcePath, int $width, int $height, int $jpegImageQuality): void
    {
        if (file_exists(($sourcePath))) {
            $thumb = ImageWorkshop::initFromPath($sourcePath);
            if ($thumb->getWidth() > $width) {
                $thumb->resizeInPixel($width, null, true);
            }
            $this->logger->debug("RemoteThumbnailCacheWrapper::createThumbnail (width: " . $width . " / height: " . $height . " / quality: " . $jpegImageQuality . ")");
            $destPath = $this->getThumbnailLocalPath($width, $height, $jpegImageQuality);
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            $thumb->save(dirname($destPath), basename($destPath), true, null, $jpegImageQuality);
        } else {
            $this->logger->error("RemoteThumbnailCacheWrapper::createThumbnail source path not found: " . $sourcePath);
        }
    }

    public function getFromRemoteURL(string $url, int $width, int $height, int $jpegImageQuality = self::DEFAULT_JPEG_IMAGE_QUALITY, bool $force = false): bool
    {
        $this->hash = sha1($url);
        $localFilePath = $this->getThumbnailLocalPath($width, $height, $jpegImageQuality);
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
                $tmpFile = tempnam(sys_get_temp_dir(), "axl");
                file_put_contents($tmpFile, $response->body);
                $this->createThumbnail($tmpFile, $width, $height, $jpegImageQuality);
                unlink($tmpFile);
                $this->path = $localFilePath;
                return (true);
            } else {
                $this->logger->critical("RemoteThumbnailCacheWrapper::getFromRemoteURL ERROR: Invalid remote HTTP code: " . $response->code);
                return (false);
            }
        } else {
            $this->path = $localFilePath;
            return (true);
        }
    }

    public function getFromLocalFilesystem(string $path, int $width, int $height, int $jpegImageQuality = self::DEFAULT_JPEG_IMAGE_QUALITY, bool $force = false): bool
    {
        $this->hash = sha1($path);
        $localFilePath = $this->getThumbnailLocalPath($width, $height, $jpegImageQuality);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - localPath => " . $localFilePath);
        if ($force && file_exists($localFilePath)) {
            $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - removing current thumbnail => " . $localFilePath);
            unlink($localFilePath);
        }
        if (!file_exists($localFilePath)) {
            if (file_exists($path)) {
                $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - local thumbnail not found... creating...");
                $this->createThumbnail($path, $width, $height, $jpegImageQuality);
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
}

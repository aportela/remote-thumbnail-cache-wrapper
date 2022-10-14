<?php

namespace aportela\RemoteThumbnailCacheWrapper;

use \PHPImageWorkshop\ImageWorkshop;

class Thumbnail
{

    protected const DEFAULT_OUTPUT_FORMAT_EXTENSION = "jpg";
    protected const JPEG_IMAGE_QUALITY = 95;

    protected $logger;
    protected $localBasePath;
    private $hash;
    public $path;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath, string $hash = "")
    {
        $this->logger = $logger;
        $this->localBasePath = rtrim($localBasePath, "/");
        if (!file_exists($this->localBasePath)) {
            mkdir($this->localBasePath, 0750);
        }
        $this->hash = $hash;
        $this->logger->debug("RemoteThumbnailCacheWrapper::__construct");
    }

    public function __destruct()
    {
        $this->logger->debug("RemoteThumbnailCacheWrapper::__destruct");
    }

    public function getThumbnailLocalPath(int $width, int $height)
    {
        return (sprintf("%s/%dx%d/%s/%s/%s.%s", $this->localBasePath, $width, $height, substr($this->hash, 0, 1), substr($this->hash, 1, 1), $this->hash, self::DEFAULT_OUTPUT_FORMAT_EXTENSION));
    }

    private function createThumbnail($sourcePath, int $width, int $height)
    {
        if (file_exists(($sourcePath))) {
            $thumb = ImageWorkshop::initFromPath($sourcePath);
            if ($thumb->getWidth() > $width) {
                $thumb->resizeInPixel($width, null, true);
            }
            $this->logger->debug("RemoteThumbnailCacheWrapper::createThumbnail (width: " . $width . " / height: " . $height . ")");
            $destPath = $this->getThumbnailLocalPath($width, $height);
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            $thumb->save(dirname($destPath), basename($destPath), true, null, self::JPEG_IMAGE_QUALITY);
        } else {
            $this->logger->error("RemoteThumbnailCacheWrapper::createThumbnail source path not found: " . $sourcePath);
        }
    }

    public function getFromRemoteURL(string $url, int $width, int $height): bool
    {
        $this->hash = sha1($url);
        $localFilePath = $this->getThumbnailLocalPath($width, $height);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromRemoteURL - localPath => " . $localFilePath);
        if (!file_exists($localFilePath)) {
            $this->logger->debug("RemoteThumbnailCacheWrapper::getFromRemoteURL - local thumbnail not found... creating...");
            $http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger);
            $response = $http->GET($url);
            if ($response->code == 200) {
                $tmpFile = tempnam(sys_get_temp_dir(), "axl");
                file_put_contents($tmpFile, $response->body);
                $this->createThumbnail($tmpFile, $width, $height);
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

    public function getFromLocalFilesystem(string $path, int $width, int $height): bool
    {
        $this->hash = sha1($path);
        $localFilePath = $this->getThumbnailLocalPath($width, $height);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - localPath => " . $localFilePath);
        if (!file_exists($localFilePath)) {
            if (file_exists($path)) {
                $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - local thumbnail not found... creating...");
                $this->createThumbnail($path, $width, $height);
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

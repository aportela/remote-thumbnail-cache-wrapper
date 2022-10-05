<?php

namespace aportela\RemoteThumbnailCacheWrapper;

use \PHPImageWorkshop\ImageWorkshop;

class Thumbnail
{

    protected const DEFAULT_OUTPUT_FORMAT_EXTENSION = "jpg";
    protected const JPEG_IMAGE_QUALITY = 95;

    protected $logger;
    protected $localBasePath;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $localBasePath)
    {
        $this->logger = $logger;
        $this->localBasePath = rtrim($localBasePath, "/");
        if (!file_exists($this->localBasePath)) {
            mkdir($this->localBasePath, 0750);
        }
        $this->logger->debug("RemoteThumbnailCacheWrapper::__construct");
    }

    public function __destruct()
    {
        $this->logger->debug("RemoteThumbnailCacheWrapper::__destruct");
    }

    public function getFromRemoteURL(string $url, int $width, int $height)
    {
        $hash = sha1($url);
        $localFilePath = sprintf("%s/%dx%d/%s/%s/%s.%s", $this->localBasePath, $width, $height, substr($hash, 0, 1), substr($hash, 1, 1), $hash, self::DEFAULT_OUTPUT_FORMAT_EXTENSION);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromRemoteURL - localPath => " . $localFilePath);
        if (!file_exists($localFilePath)) {

            $this->logger->debug("RemoteThumbnailCacheWrapper::getFromRemoteURL - local thumbnail not found... creating...");
            $http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger);
            $response = $http->GET($url);
            if ($response->code == 200) {
                $tmpFile = tempnam(sys_get_temp_dir(), "axl");
                file_put_contents($tmpFile, $response->body);
                $thumb = ImageWorkshop::initFromPath($tmpFile);
                if ($thumb->getWidth() > $width) {
                    $thumb->resizeInPixel($width, null, true);
                }
                $thumb->save(dirname($localFilePath), basename($localFilePath), true, null, self::JPEG_IMAGE_QUALITY);
                unlink($tmpFile);
                readfile($localFilePath);
            } else {
                $this->logger->critical("RemoteThumbnailCacheWrapper::getFromRemoteURL ERROR: Invalid remote HTTP code: " . $response->code);
            }
        } else {
            readfile($localFilePath);
        }
    }

    public function getFromLocalFilesystem(string $path, int $width, int $height)
    {
        $hash = sha1($path);
        $localFilePath = sprintf("%s/%dx%d/%s/%s/%s.%s", $this->localBasePath, $width, $height, substr($hash, 0, 1), substr($hash, 1, 1), $hash, self::DEFAULT_OUTPUT_FORMAT_EXTENSION);
        $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - localPath => " . $localFilePath);
        if (!file_exists($localFilePath)) {
            if (file_exists($path)) {
                $this->logger->debug("RemoteThumbnailCacheWrapper::getFromLocalFilesystem - local thumbnail not found... creating...");
                $thumb = ImageWorkshop::initFromPath($path);
                if ($thumb->getWidth() > $width) {
                    $thumb->resizeInPixel($width, null, true);
                }
                $thumb->save(dirname($localFilePath), basename($localFilePath), true, null, self::JPEG_IMAGE_QUALITY);
                readfile($localFilePath);
            } else {
                $this->logger->critical("RemoteThumbnailCacheWrapper::getFromLocalFilesystem ERROR: path not found: " . $path);
            }
        } else {
            readfile($localFilePath);
        }
    }
}

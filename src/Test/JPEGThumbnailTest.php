<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class JPEGThumbnailTest extends \aportela\RemoteThumbnailCacheWrapper\Test\BaseTest
{
    public function testInvalidSetDimensions(): void
    {
        $this->expectException(\aportela\RemoteThumbnailCacheWrapper\Exception\InvalidDimensionsException::class);
        $this->expectExceptionMessageMatches("/^Invalid dimensions:.*/");
        $localFilenameResource = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $localFilenameResource);
        $jpegThumbnail->setDimensions(0, 0, false);
    }

    public function testValidSetDimensions(): void
    {
        $localFilenameResource = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $localFilenameResource);
        $jpegThumbnail->setDimensions(640, 480, false);

        $path = $jpegThumbnail->get();
        $this->assertIsString($path);
        $this->assertFileExists($path);
    }

    public function testInvalidSetQuality(): void
    {
        $this->expectException(\aportela\RemoteThumbnailCacheWrapper\Exception\InvalidQualityException::class);
        $this->expectExceptionMessageMatches("/^Invalid quality:.*/");
        $localFilenameResource = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $localFilenameResource);
        $jpegThumbnail->setQuality(200, false);
    }

    public function testValidSetQuality(): void
    {
        $localFilenameResource = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $localFilenameResource);
        $jpegThumbnail->setQuality(50, false);

        $path = $jpegThumbnail->get();
        $this->assertIsString($path);
        $this->assertFileExists($path);
    }

    public function testRemoteInvalidUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/^Invalid url: " . self::REMOTE_URL_INVALID . "$/");
        new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_INVALID);
    }

    public function testRemote404Image(): void
    {
        $urlSource = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_NOT_FOUND);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $urlSource);
        $this->assertFalse($jpegThumbnail->get());
    }

    public function testRemote200NotImageMime(): void
    {
        $urlSource = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_INVALID_IMAGE_MIME_TYPE);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $urlSource);
        $this->assertFalse($jpegThumbnail->get());
    }

    public function testRemote200ValidImage(): void
    {
        $urlSource = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_VALID_IMAGE);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $urlSource);
        $path = $jpegThumbnail->get();
        $this->assertIsString($path);
        $this->assertFileExists($path);
    }

    public function testRemote200ValidImageForce(): void
    {
        $urlSource = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_VALID_IMAGE);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $urlSource);
        $path = $jpegThumbnail->get();
        $this->assertIsString($path);
        $this->assertFileExists($path);
    }

    public function testLocalNotExistentImage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/^Local filename \(.*\) not found$/");
        new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_NOT_EXISTENT);
    }

    public function testLocalExistingImage(): void
    {
        $localFilenameResource = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $localFilenameResource);
        $path = $jpegThumbnail->get();
        $this->assertIsString($path);
        $this->assertFileExists($path);
    }

    public function testLocalExistingImageForce(): void
    {
        $localFilenameResource = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $jpegThumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $localFilenameResource);
        $path = $jpegThumbnail->get(true);
        $this->assertIsString($path);
        $this->assertFileExists($path);
    }
}

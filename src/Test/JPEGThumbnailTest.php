<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class JPEGThumbnailTest extends \aportela\RemoteThumbnailCacheWrapper\Test\BaseTest
{
    public function testInvalidSetDimensions(): void
    {
        $this->expectException(\aportela\RemoteThumbnailCacheWrapper\Exception\InvalidDimensionsException::class);
        $this->expectExceptionMessageMatches("/^Invalid dimensions:.*/");
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $thumbnail->setDimensions(0, 0, false);
    }

    public function testValidSetDimensions(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $thumbnail->setDimensions(640, 480, false);
        $path = $thumbnail->get();
        $this->assertNotFalse($path);
        $this->assertFileExists($path);
    }

    public function testInvalidSetQuality(): void
    {
        $this->expectException(\aportela\RemoteThumbnailCacheWrapper\Exception\InvalidQualityException::class);
        $this->expectExceptionMessageMatches("/^Invalid quality:.*/");
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $thumbnail->setQuality(200, false);
    }

    public function testValidSetQuality(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $thumbnail->setQuality(50, false);
        $path = $thumbnail->get();
        $this->assertNotFalse($path);
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
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_NOT_FOUND);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $this->assertFalse($thumbnail->get());
    }

    public function testRemote200NotImageMime(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_INVALID_IMAGE_MIME_TYPE);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $this->assertFalse($thumbnail->get());
    }

    public function testRemote200ValidImage(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_VALID_IMAGE);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->get();
        $this->assertNotFalse($path);
        $this->assertFileExists($path);
    }

    public function testRemote200ValidImageForce(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\URLSource(self::REMOTE_URL_VALID_IMAGE);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->get();
        $this->assertNotFalse($path);
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
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->get();
        $this->assertNotFalse($path);
        $this->assertFileExists($path);
    }

    public function testLocalExistingImageForce(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\Source\LocalFilenameResource(self::LOCAL_FILE_EXISTENT);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->get(true);
        $this->assertNotFalse($path);
        $this->assertFileExists($path);
    }
}

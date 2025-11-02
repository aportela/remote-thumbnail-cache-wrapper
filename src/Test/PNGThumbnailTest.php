<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class PNGThumbnailTest extends \aportela\RemoteThumbnailCacheWrapper\Test\BaseTest
{
    /*
    public function testRemoteNotExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->getFromRemoteURL(self::REMOTE_URL_404));
        $this->assertNull($thumbnail->path);
    }

    public function testRemoteExistsNotImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200_NOT_IMAGE));
        $this->assertFalse($thumbnail->getFromRemoteURL(self::REMOTE_URL_200_NOT_IMAGE));
        $this->assertFalse($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200_NOT_IMAGE));
        $this->assertEmpty($thumbnail->path);
    }

    public function testRemoteExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200));
        $this->assertTrue($thumbnail->getFromRemoteURL(self::REMOTE_URL_200));
        $this->assertTrue($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testRemoteExistsImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromRemoteURL(self::REMOTE_URL_200, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalNotExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_NOT_EXISTS));
        $this->assertNull($thumbnail->path);
    }

    public function testLocalExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->localFilesystemExistsInCache(self::LOCAL_FILE_EXISTS));
        $this->assertTrue($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS));
        $this->assertTrue($thumbnail->localFilesystemExistsInCache(self::LOCAL_FILE_EXISTS));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalExistsImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testGetFromCacheNotExists(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->getFromCache(sha1(self::LOCAL_FILE_NOT_EXISTS)));
    }
    */
}

<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class JPEGThumbnailTest extends \aportela\RemoteThumbnailCacheWrapper\Test\BaseTest
{
    public function testRemoteNotExistsImage(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\URLSource(self::REMOTE_URL_404);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->getFromRemoteURL(self::REMOTE_URL_404, false);
        $this->assertFalse($path);
    }

    public function testRemoteExistsNotImage(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\URLSource(self::REMOTE_URL_200_NOT_IMAGE);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->getFromRemoteURL(self::REMOTE_URL_200_NOT_IMAGE, false);
        $this->assertFalse($path);
    }

    public function testRemoteExistsImage(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\URLSource(self::REMOTE_URL_200_NOT_IMAGE);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->getFromRemoteURL(self::REMOTE_URL_200_NOT_IMAGE, false);
        if ($path !== false) {
            $this->assertFileExists($path);
        } else {
            $this->assertFalse($path);
        }
    }

    public function testRemoteExistsImageForce(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\URLSource(self::REMOTE_URL_200_NOT_IMAGE);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->getFromRemoteURL(self::REMOTE_URL_200_NOT_IMAGE, true);
        if ($path !== false) {
            $this->assertFileExists($path);
        } else {
            $this->assertFalse($path);
        }
    }

    public function testLocalNotExistsImage(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/^Local filename \(.*\) not found$/");
        $source = new \aportela\RemoteThumbnailCacheWrapper\LocalFilenameResource(self::LOCAL_FILE_NOT_EXISTS);
    }

    public function testLocalExistsImage(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\LocalFilenameResource(self::LOCAL_FILE_EXISTS);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS, false);
        if ($path !== false) {
            $this->assertFileExists($path);
        } else {
            $this->assertFalse($path);
        }
    }

    public function testLocalExistsImageForce(): void
    {
        $source = new \aportela\RemoteThumbnailCacheWrapper\LocalFilenameResource(self::LOCAL_FILE_EXISTS);
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$cachePath, $source);
        $path = $thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS, true);
        if ($path !== false) {
            $this->assertFileExists($path);
        } else {
            $this->assertFalse($path);
        }
    }

    /*

    public function testGetFromCacheExists(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
        $this->assertTrue($thumbnail->getFromCache(sha1(self::LOCAL_FILE_EXISTS)));
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

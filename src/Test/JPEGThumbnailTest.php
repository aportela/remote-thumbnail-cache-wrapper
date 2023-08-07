<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

class JPEGThumbnailTest extends \PHPUnit\Framework\TestCase
{
    protected static $logger;

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        self::$logger = new \Psr\Log\NullLogger("");
    }

    /**
     * Initialize the test case
     * Called for every defined test
     */
    public function setUp(): void
    {
    }

    /**
     * Clean up the test case, called for every defined test
     */
    public function tearDown(): void
    {
    }

    /**
     * Clean up the whole test class
     */
    public static function tearDownAfterClass(): void
    {
    }

    public function testRemoteNotExistentImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);
        $url = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/2.0pre/src/Test/404.jpg";
        $this->assertFalse($thumbnail->getFromRemoteURL($url));
        $this->assertNull($thumbnail->path);
    }

    public function testRemoteExistentImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);
        // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
        $url = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/2.0pre/src/Test/200.jpg";
        $this->assertTrue($thumbnail->getFromRemoteURL($url));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testRemoteExistentImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);
        // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
        $url = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/2.0pre/src/Test/200.jpg";
        $this->assertTrue($thumbnail->getFromRemoteURL($url, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalNotExistentImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);
        $localFile = sprintf("%s%s%s", __DIR__, DIRECTORY_SEPARATOR, "400.jpg");
        $this->assertFalse($thumbnail->getFromLocalFilesystem($localFile));
        $this->assertNull($thumbnail->path);
    }

    public function testLocalExistentImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);
        // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
        $localFile = sprintf("%s%s%s", __DIR__, DIRECTORY_SEPARATOR, "200.jpg");
        $this->assertTrue($thumbnail->getFromLocalFilesystem($localFile));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalExistentImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_JPEG_IMAGE_QUALITY);
        // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
        $localFile = sprintf("%s%s%s", __DIR__, DIRECTORY_SEPARATOR, "200.jpg");
        $this->assertTrue($thumbnail->getFromLocalFilesystem($localFile, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }
}

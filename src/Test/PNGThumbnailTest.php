<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class PNGThumbnailTest extends \PHPUnit\Framework\TestCase
{
    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    private const REMOTE_URL_200 = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/200.jpg";
    private const REMOTE_URL_404 = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/404.jpg";
    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    private const LOCAL_FILE_EXISTS = __DIR__ . DIRECTORY_SEPARATOR . "200.jpg";
    private const LOCAL_FILE_NOT_EXISTS = __DIR__ . DIRECTORY_SEPARATOR . "400.jpg";

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
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->getFromRemoteURL(self::REMOTE_URL_404));
        $this->assertNull($thumbnail->path);
    }

    public function testRemoteExistentImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromRemoteURL(self::REMOTE_URL_200));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testRemoteExistentImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $url = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/2.0pre/src/Test/200.jpg";
        $this->assertTrue($thumbnail->getFromRemoteURL(self::REMOTE_URL_200, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalNotExistentImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_NOT_EXISTS));
        $this->assertNull($thumbnail->path);
    }

    public function testLocalExistentImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalExistentImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\PNGThumbnail(self::$logger, sys_get_temp_dir());
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\PNGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }
}

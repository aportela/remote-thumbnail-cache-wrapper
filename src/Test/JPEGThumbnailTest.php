<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

final class JPEGThumbnailTest extends \PHPUnit\Framework\TestCase
{
    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    private const REMOTE_URL_200 = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/200.jpg";
    private const REMOTE_URL_200_NOT_IMAGE = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/README.md";
    private const REMOTE_URL_404 = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/404.jpg";
    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    private const LOCAL_FILE_EXISTS = __DIR__ . DIRECTORY_SEPARATOR . "200.jpg";
    private const LOCAL_FILE_NOT_EXISTS = __DIR__ . DIRECTORY_SEPARATOR . "400.jpg";

    protected static $logger;
    protected static string $baseDir;

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        self::$logger = new \Psr\Log\NullLogger("");
        $newPath = tempnam(sys_get_temp_dir(), 'xyz');
        unlink($newPath);
        mkdir($newPath);
        self::$baseDir = $newPath;
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

    public function testRemoteNotExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->getFromRemoteURL(self::REMOTE_URL_404));
        $this->assertNull($thumbnail->path);
    }

    public function testRemoteExistsNotImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200_NOT_IMAGE));
        $this->assertFalse($thumbnail->getFromRemoteURL(self::REMOTE_URL_200_NOT_IMAGE));
        $this->assertFalse($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200_NOT_IMAGE));
        $this->assertEmpty($thumbnail->path);
    }
    public function testRemoteExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200));
        $this->assertTrue($thumbnail->getFromRemoteURL(self::REMOTE_URL_200));
        $this->assertTrue($thumbnail->remoteURLExistsInCache(self::REMOTE_URL_200));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testRemoteExistsImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromRemoteURL(self::REMOTE_URL_200, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalNotExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_NOT_EXISTS));
        $this->assertNull($thumbnail->path);
    }

    public function testLocalExistsImage(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertFalse($thumbnail->localFilesystemExistsInCache(self::LOCAL_FILE_EXISTS));
        $this->assertTrue($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS));
        $this->assertTrue($thumbnail->localFilesystemExistsInCache(self::LOCAL_FILE_EXISTS));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }

    public function testLocalExistsImageForce(): void
    {
        $thumbnail = new \aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail(self::$logger, self::$baseDir);
        $thumbnail->setDimensions(64, 43);
        $thumbnail->setQuality(\aportela\RemoteThumbnailCacheWrapper\JPEGThumbnail::DEFAULT_IMAGE_QUALITY);
        $this->assertTrue($thumbnail->getFromLocalFilesystem(self::LOCAL_FILE_EXISTS, true));
        $this->assertNotEmpty($thumbnail->path);
        $this->assertFileExists($thumbnail->path);
    }


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
}

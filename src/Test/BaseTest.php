<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

class BaseTest extends \PHPUnit\Framework\TestCase
{
    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    protected const REMOTE_URL_200 = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/200.jpg";
    protected const REMOTE_URL_200_NOT_IMAGE = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/README.md";
    protected const REMOTE_URL_404 = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/404.jpg";
    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    protected const LOCAL_FILE_EXISTS = __DIR__ . DIRECTORY_SEPARATOR . "200.jpg";
    protected const LOCAL_FILE_NOT_EXISTS = __DIR__ . DIRECTORY_SEPARATOR . "400.jpg";

    protected static \Psr\Log\NullLogger $logger;
    protected static string $cachePath;

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        self::$logger = new \Psr\Log\NullLogger();
        self::$cachePath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "cache";
    }

    /**
     * Initialize the test case
     * Called for every defined test
     */
    public function setUp(): void {}

    /**
     * Clean up the test case, called for every defined test
     */
    public function tearDown(): void {}

    /**
     * Clean up the whole test class
     */
    public static function tearDownAfterClass(): void {}
}

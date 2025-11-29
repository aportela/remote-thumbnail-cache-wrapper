<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Test;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

abstract class BaseTest extends \PHPUnit\Framework\TestCase
{
    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    protected const REMOTE_URL_VALID_IMAGE = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/200.jpg";

    protected const REMOTE_URL_INVALID_IMAGE_MIME_TYPE = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/README.md";

    protected const REMOTE_URL_NOT_FOUND = "https://raw.githubusercontent.com/aportela/remote-thumbnail-cache-wrapper/main/src/Test/404.jpg";

    protected const REMOTE_URL_INVALID = "THIS_IS_NOT_A_VALID_URL";

    // image credits: https://unsplash.com/es/fotos/89xuP-XmyrA (Caspar Camille Rubin)
    protected const LOCAL_FILE_EXISTENT = __DIR__ . DIRECTORY_SEPARATOR . "200.jpg";

    protected const LOCAL_FILE_NOT_EXISTENT = __DIR__ . DIRECTORY_SEPARATOR . "400.jpg";

    protected static \Psr\Log\NullLogger $logger;

    protected static string $cachePath;

    /**
     * Called once just like normal constructor
     */
    public static function setUpBeforeClass(): void
    {
        self::$logger = new \Psr\Log\NullLogger();
        self::$cachePath = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . "cache";
    }

    /**
     * Initialize the test case
     * Called for every defined test
     */
    protected function setUp(): void {}

    /**
     * Clean up the test case, called for every defined test
     */
    protected function tearDown(): void {}

    /**
     * Clean up the whole test class
     */
    public static function tearDownAfterClass(): void {}
}

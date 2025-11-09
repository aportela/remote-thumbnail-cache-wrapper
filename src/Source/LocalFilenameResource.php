<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Source;

final class LocalFilenameResource implements \aportela\RemoteThumbnailCacheWrapper\Source\ISource
{
    private string $localFilename;

    private string $hash;

    public function __construct(string $localFilename)
    {
        if (file_exists($localFilename)) {
            $this->localFilename = $localFilename;
            $this->hash = sha1($localFilename);
        } else {
            throw new \InvalidArgumentException(sprintf('Local filename (%s) not found', $localFilename));
        }
    }

    public function getResource(): string
    {
        return ($this->localFilename);
    }

    public function getHash(): string
    {
        return ($this->hash);
    }
}

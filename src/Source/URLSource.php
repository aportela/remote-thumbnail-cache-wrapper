<?php

namespace aportela\RemoteThumbnailCacheWrapper\Source;

final class URLSource implements \aportela\RemoteThumbnailCacheWrapper\Source\ISource
{
    private string $url;
    private string $hash;

    public function __construct(string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $url;
            $this->hash = sha1($url);
        } else {
            throw new \InvalidArgumentException("Invalid url: {$url}");
        }
    }

    public function __destruct()
    {
    }

    public function getResource(): string
    {
        return ($this->url);
    }

    public function getHash(): string
    {
        return ($this->hash);
    }
}

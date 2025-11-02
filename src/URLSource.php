<?php

namespace aportela\RemoteThumbnailCacheWrapper;

final class URLSource implements \aportela\RemoteThumbnailCacheWrapper\ISource
{
    private string $url;
    private string $hash;

    public function __construct(string $url)
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl !== false && $parsedUrl !== null) {
            $this->url = $url;
            $this->hash = sha1($url);
        } else {
            throw new \Exception("Invalid url: {$url}");
        }
    }

    public function __destruct() {}

    public function getResource(): string
    {
        return ($this->url);
    }

    public function getHash(): string
    {
        return ($this->hash);
    }
}

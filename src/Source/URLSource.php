<?php

namespace aportela\RemoteThumbnailCacheWrapper\Source;

final class URLSource implements \aportela\RemoteThumbnailCacheWrapper\Source\ISource
{
    private string $url;
    private string $hash;

    public function __construct(string $url)
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl !== false && ! empty($parsedUrl["scheme"]) && ! empty($parsedUrl["host"])) {
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

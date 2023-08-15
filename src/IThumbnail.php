<?php

namespace aportela\RemoteThumbnailCacheWrapper;

interface iThumbnail
{
    public function getFromRemoteURL(string $url, bool $force = false): bool;

    public function getFromLocalFilesystem(string $path, bool $force = false): bool;

    public function getFromCache(string $hash): bool;
}

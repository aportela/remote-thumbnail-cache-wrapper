<?php

namespace aportela\RemoteThumbnailCacheWrapper;

interface ISource
{
    public function getResource(): string;
    public function getHash(): string;
}

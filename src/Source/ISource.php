<?php

declare(strict_types=1);

namespace aportela\RemoteThumbnailCacheWrapper\Source;

interface ISource
{
    public function getResource(): string;

    public function getHash(): string;
}

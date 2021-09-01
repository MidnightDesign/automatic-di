<?php

declare(strict_types=1);

namespace Midnight\AutomaticDi\Cache;

use Midnight\AutomaticDi\Cache\Exception\CacheMissException;

interface CacheInterface
{
    public function has(string $key): bool;

    /**
     * @throws CacheMissException
     */
    public function get(string $key): string;

    public function set(string $key, string $value): void;
}

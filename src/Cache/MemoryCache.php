<?php declare(strict_types = 1);

namespace Midnight\AutomaticDi\Cache;

use Midnight\AutomaticDi\Cache\Exception\CacheMissException;

class MemoryCache implements CacheInterface
{
    /** @var string[] */
    private $cache = [];

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    public function get(string $key): string
    {
        if (!$this->has($key)) {
            throw CacheMissException::fromKey($key);
        }
        return $this->cache[$key];
    }

    public function set(string $key, string $value)
    {
        $this->cache[$key] = $value;
    }
}

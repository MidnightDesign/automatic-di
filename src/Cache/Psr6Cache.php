<?php declare(strict_types = 1);

namespace Midnight\AutomaticDi\Cache;

use Midnight\AutomaticDi\Cache\Exception\CacheMissException;
use Psr\Cache\CacheItemPoolInterface;

class Psr6Cache implements CacheInterface
{
    /** @var CacheItemPoolInterface */
    private $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function has(string $key): bool
    {
        return $this->cache->hasItem($key);
    }

    public function get(string $key): string
    {
        $item = $this->cache->getItem($key);
        if (!$item->isHit()) {
            throw CacheMissException::fromKey($key);
        }
        return $item->get();
    }

    public function set(string $key, string $value)
    {
        $item = $this->cache->getItem($key);
        $item->set($value);
        $this->cache->save($item);
    }
}

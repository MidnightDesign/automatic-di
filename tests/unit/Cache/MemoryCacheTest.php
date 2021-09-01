<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi\Cache;

use Midnight\AutomaticDi\Cache\Exception\CacheMissException;
use Midnight\AutomaticDi\Cache\MemoryCache;
use PHPUnit\Framework\TestCase;

class MemoryCacheTest extends TestCase
{
    private MemoryCache $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new MemoryCache();
    }

    public function testHasNot(): void
    {
        self::assertFalse($this->cache->has('nope'));
    }

    public function testHas(): void
    {
        $this->cache->set('foo', 'bar');

        self::assertTrue($this->cache->has('foo'));
    }

    public function testFailingGet(): void
    {
        $this->expectException(CacheMissException::class);

        $this->cache->get('nope');
    }

    public function testSetAndGet(): void
    {
        $this->cache->set('foo', 'bar');

        self::assertSame('bar', $this->cache->get('foo'));
    }
}

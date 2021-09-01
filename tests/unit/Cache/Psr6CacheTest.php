<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi\Cache;

use Midnight\AutomaticDi\Cache\Exception\CacheMissException;
use Midnight\AutomaticDi\Cache\Psr6Cache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class Psr6CacheTest extends TestCase
{
    private Psr6Cache $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new Psr6Cache(new ArrayAdapter());
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

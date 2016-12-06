<?php declare(strict_types = 1);

namespace MidnightTest\Unit\AutomaticDi\Cache;

use Midnight\AutomaticDi\Cache\Exception\CacheMissException;
use Midnight\AutomaticDi\Cache\MemoryCache;
use PHPUnit\Framework\TestCase;

class MemoryCacheTest extends TestCase
{
    /** @var MemoryCache */
    private $cache;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = new MemoryCache;
    }

    public function testHasNot()
    {
        $this->assertFalse($this->cache->has('nope'));
    }

    public function testHas()
    {
        $this->cache->set('foo', 'bar');

        $this->assertTrue($this->cache->has('foo'));
    }

    public function testFailingGet()
    {
        $this->expectException(CacheMissException::class);

        $this->cache->get('nope');
    }

    public function testSetAndGet()
    {
        $this->cache->set('foo', 'bar');

        $this->assertSame('bar', $this->cache->get('foo'));
    }
}

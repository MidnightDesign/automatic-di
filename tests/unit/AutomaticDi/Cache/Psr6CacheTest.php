<?php declare(strict_types = 1);

namespace MidnightTest\Unit\AutomaticDi\Cache;

use Midnight\AutomaticDi\Cache\Exception\CacheMissException;
use Midnight\AutomaticDi\Cache\Psr6Cache;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class Psr6CacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var Psr6Cache */
    private $cache;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = new Psr6Cache(new ArrayAdapter);
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

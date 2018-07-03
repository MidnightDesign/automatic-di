<?php

namespace MidnightTest\Unit\AutomaticDi;

use InvalidArgumentException;
use Midnight\AutomaticDi\AutomaticDiConfig;
use PHPUnit_Framework_TestCase;

class AutomaticDiConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGetPreferences()
    {
        $config = AutomaticDiConfig::fromArray([
            'preferences' => [FooInterface::class => Foo::class],
            'classes' => [],
        ]);

        $preferences = $config->getPreferences();
        $this->assertArrayHasKey(FooInterface::class, $preferences);
        $this->assertSame(Foo::class, $preferences[FooInterface::class]);
    }

    public function testGetClassPreferences()
    {
        $config = AutomaticDiConfig::fromArray([
            'preferences' => [],
            'classes' => [Baz::class => ['foo' => Foo::class]],
        ]);

        $classPreferences = $config->getClassPreferences();
        $this->assertArrayHasKey(Baz::class, $classPreferences);
        $this->assertInternalType('array', $classPreferences);
        $this->assertArrayHasKey('foo', $classPreferences[Baz::class]);
        $this->assertSame(Foo::class, $classPreferences[Baz::class]['foo']);
    }

    public function invalidConfigData()
    {
        return [
            [[]],
            [['preferences' => []]],
            [['classes' => []]],
        ];
    }

    /**
     * @dataProvider invalidConfigData
     */
    public function testInvalidConfig(array $config)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        AutomaticDiConfig::fromArray($config);
    }
}

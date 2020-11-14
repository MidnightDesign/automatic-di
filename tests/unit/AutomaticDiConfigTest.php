<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

use InvalidArgumentException;
use Midnight\AutomaticDi\AutomaticDiConfig;
use PHPUnit\Framework\TestCase;

class AutomaticDiConfigTest extends TestCase
{
    public function testGetPreferences(): void
    {
        $config = AutomaticDiConfig::fromArray(
            [
                'preferences' => [FooInterface::class => Foo::class],
                'classes' => [],
            ]
        );

        $preferences = $config->getPreferences();
        self::assertArrayHasKey(FooInterface::class, $preferences);
        self::assertSame(Foo::class, $preferences[FooInterface::class]);
    }

    public function testGetClassPreferences(): void
    {
        $config = AutomaticDiConfig::fromArray(
            [
                'preferences' => [],
                'classes' => [Baz::class => ['foo' => Foo::class]],
            ]
        );

        $classPreferences = $config->getClassPreferences();
        self::assertArrayHasKey(Baz::class, $classPreferences);
        self::assertArrayHasKey('foo', $classPreferences[Baz::class]);
        self::assertSame(Foo::class, $classPreferences[Baz::class]['foo']);
    }

    /**
     * @return array<array<array<string, mixed>>>
     */
    public function invalidConfigData(): array
    {
        return [
            [[]],
            [['preferences' => []]],
            [['classes' => []]],
        ];
    }

    /**
     * @dataProvider invalidConfigData
     * @param array<string, mixed> $config
     */
    public function testInvalidConfig(array $config): void
    {
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore-next-line
        AutomaticDiConfig::fromArray($config);
    }
}

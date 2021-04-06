<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

use LogicException;
use Midnight\AutomaticDi\AutomaticDiConfig;
use Midnight\AutomaticDi\AutomaticDiContainer;
use MidnightTest\Unit\AutomaticDi\TestDouble\MemoryContainer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use const PHP_VERSION_ID;

class AutomaticDiContainerTest extends TestCase
{
    private MemoryContainer $externalContainer;
    /** @var AutomaticDiConfig|MockObject */
    private $config;
    private AutomaticDiContainer $container;

    protected function setUp(): void
    {
        $this->externalContainer = new MemoryContainer();
        $this->config = $this->createMock(AutomaticDiConfig::class);
        $this->container = new AutomaticDiContainer($this->externalContainer, $this->config);
    }

    public function testGetSimple(): void
    {
        $this->configureExternalContainer(
            [
                Foo::class => new Foo(),
            ]
        );

        $requiresFoo = $this->container->get(RequiresFoo::class);

        self::assertInstanceOf(RequiresFoo::class, $requiresFoo);
    }

    public function testPreference(): void
    {
        $this->configureContainer(
            [
                'preferences' => [
                    FooInterface::class => Foo::class,
                ],
            ]
        );
        $this->configureExternalContainer(
            [
                Foo::class => new Foo(),
            ]
        );

        $foo = $this->container->get(FooInterface::class);

        self::assertInstanceOf(Foo::class, $foo);
    }

    public function testHasReturnsTrueIfClassExists(): void
    {
        self::assertTrue($this->container->has(Foo::class));
    }

    public function testHasReturnsFalseIfClassDoesNotExist(): void
    {
        /** @noinspection PhpUndefinedClassInspection */
        self::assertFalse($this->container->has(DoesNotExist::class)); // @phpstan-ignore-line
    }

    public function testHasReturnsTrueIfPreferenceForInterfaceExists(): void
    {
        $this->configureContainer(
            [
                'preferences' => [
                    FooInterface::class => Foo::class,
                ],
            ]
        );

        self::assertTrue($this->container->has(FooInterface::class));
    }

    public function testHasReturnsFalseIfNoPreferenceExistsForInterface(): void
    {
        $this->configureContainer(
            [
                'preferences' => [],
            ]
        );

        self::assertFalse($this->container->has(FooInterface::class));
    }

    public function testClassPreferenceForInterface(): void
    {
        $this->configureContainer(
            [
                'classes' => [
                    RequiresFooInterface::class => [
                        'foo' => Foo::class,
                    ],
                ],
            ]
        );
        $this->configureExternalContainer(
            [
                Foo::class => new Foo(),
            ]
        );

        $requiresFooInterface = $this->container->get(RequiresFooInterface::class);

        self::assertInstanceOf(RequiresFooInterface::class, $requiresFooInterface);
        self::assertInstanceOf(Foo::class, $requiresFooInterface->foo);
    }

    public function testPreferenceForDependency(): void
    {
        $this->configureContainer(
            [
                'preferences' => [
                    FooInterface::class => Foo::class,
                ],
            ]
        );
        $this->configureExternalContainer(
            [
                Foo::class => new Foo(),
            ]
        );

        $requiresFooInterface = $this->container->get(RequiresFooInterface::class);

        self::assertInstanceOf(RequiresFooInterface::class, $requiresFooInterface);
        self::assertInstanceOf(Foo::class, $requiresFooInterface->foo);
    }

    public function testMissingTypeHintAndNoConfig(): void
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage(
            'Missing preference for constructor parameter noTypeHint of MidnightTest\Unit\AutomaticDi\MissingTypeHint.'
        );
        $this->container->get(MissingTypeHint::class);
    }

    public function testMissingTypeHintWithConfig(): void
    {
        $this->configureContainer(
            [
                'classes' => [
                    MissingTypeHint::class => [
                        'noTypeHint' => Foo::class,
                    ],
                ],
            ]
        );
        $this->configureExternalContainer(
            [
                Foo::class => new Foo(),
            ]
        );

        $missingTypeHint = $this->container->get(MissingTypeHint::class);

        self::assertInstanceOf(MissingTypeHint::class, $missingTypeHint);
        self::assertInstanceOf(Foo::class, $missingTypeHint->noTypeHint);
    }

    public function testNoConstructor(): void
    {
        $foo = $this->container->get(Foo::class);

        self::assertInstanceOf(Foo::class, $foo);
    }

    public function testConfigureParameterDefinedInParentClass(): void
    {
        $this->configureContainer(
            [
                'classes' => [
                    Bar::class => [
                        'foo' => Foo::class,
                    ],
                ],
            ]
        );
        $this->configureExternalContainer(
            [
                Foo::class => new Foo(),
            ]
        );

        $bar = $this->container->get(Bar::class);

        self::assertInstanceOf(Bar::class, $bar);
        self::assertInstanceOf(Foo::class, $bar->foo);
    }

    public function testDefaultValueIsUsed(): void
    {
        $object = $this->container->get(HasDefaultValue::class);

        self::assertInstanceOf(HasDefaultValue::class, $object);
        self::assertSame(23, $object->value);
    }

    public function testMultipleArgs(): void
    {
        $this->configureExternalContainer(
            [
                Foo::class => new Foo(),
                OtherClass::class => new OtherClass(),
            ]
        );

        $requiresFoo = $this->container->get(RequiresFooAndOtherClass::class);

        self::assertInstanceOf(RequiresFooAndOtherClass::class, $requiresFoo);
    }

    public function testNonOptionalParameterWithUnionTypeThrowsException(): void
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped('This test only works in PHP 8.');
        }
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Constructor parameter fooBar is a union type, which is not supported, yet.');

        $this->container->get(RequiresUnionType::class);
    }

    public function testWithUnionTypeParameterUsesDefaultValue(): void
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped('This test only works in PHP 8.');
        }
        $object = $this->container->get(UnionTypeWithDefaultValue::class);

        self::assertInstanceOf(UnionTypeWithDefaultValue::class, $object);
        self::assertSame(42, $object->bazInt);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureExternalContainer(array $config): void
    {
        $this->externalContainer->setServices($config);
    }

    /**
     * @param array<string, array<string, array<string, string>|string>> $config
     */
    private function configureContainer(array $config): void
    {
        $classPreferences = $config['classes'] ?? [];
        $this->config->method('getClassPreferences')->willReturn($classPreferences);
        $preferences = $config['preferences'] ?? [];
        $this->config->method('getPreferences')->willReturn($preferences);
    }
}

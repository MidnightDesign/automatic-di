<?php

namespace MidnightTest\Unit\AutomaticDi;

use Midnight\AutomaticDi\AutomaticDiConfig;
use Midnight\AutomaticDi\AutomaticDiContainer;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

/**
 * Class AutomaticDiContainerTest
 *
 * @package MidnightTest\Unit\AutomaticDi
 */
class AutomaticDiContainerTest extends PHPUnit_Framework_TestCase
{
    /** @var ServiceManager */
    private $serviceManager;
    /** @var AutomaticDiConfig|PHPUnit_Framework_MockObject_MockObject */
    private $config;
    /** @var AutomaticDiContainer */
    private $container;

    public function setUp()
    {
        $this->serviceManager = new ServiceManager(new Config([]));
        $this->config = $this->getMockBuilder(AutomaticDiConfig::class)->disableOriginalConstructor()->getMock();
        $this->container = new AutomaticDiContainer($this->serviceManager, $this->config);
    }

    public function testGetSimple()
    {
        $this->configureServiceManager([
            Foo::class => new Foo,
        ]);

        $requiresFoo = $this->container->get(RequiresFoo::class);

        $this->assertInstanceOf(RequiresFoo::class, $requiresFoo);
        $this->assertInstanceOf(Foo::class, $requiresFoo->foo);
    }

    public function testPreference()
    {
        $this->configureContainer([
            'preferences' => [
                FooInterface::class => Foo::class,
            ],
        ]);
        $this->configureServiceManager([
            Foo::class => new Foo,
        ]);

        $foo = $this->container->get(FooInterface::class);

        $this->assertInstanceOf(Foo::class, $foo);
    }

    public function testHasReturnsTrueIfClassExists()
    {
        $this->assertTrue($this->container->has(Foo::class));
    }

    public function testHasReturnsTrueIfClassDoesNotExist()
    {
        /** @noinspection PhpUndefinedClassInspection */
        $this->assertFalse($this->container->has(DoesNotExist::class));
    }

    public function testHasReturnsTrueIfPreferenceForInterfaceExists()
    {
        $this->configureContainer([
            'preferences' => [
                FooInterface::class => Foo::class,
            ],
        ]);

        $this->assertTrue($this->container->has(FooInterface::class));
    }

    public function testHasReturnsFalseIfNoPreferenceExistsForInterface()
    {
        $this->configureContainer([
            'preferences' => [],
        ]);

        $this->assertFalse($this->container->has(FooInterface::class));
    }

    public function testClassPreferenceForInterface()
    {
        $this->configureContainer([
            'classes' => [
                RequiresFooInterface::class => [
                    'foo' => Foo::class,
                ],
            ],
        ]);
        $this->configureServiceManager([
            Foo::class => new Foo,
        ]);

        $requiresFooInterface = $this->container->get(RequiresFooInterface::class);

        $this->assertInstanceOf(RequiresFooInterface::class, $requiresFooInterface);
        $this->assertInstanceOf(Foo::class, $requiresFooInterface->foo);
    }

    public function testPreferenceForDependency()
    {
        $this->configureContainer([
            'preferences' => [
                FooInterface::class => Foo::class,
            ],
        ]);
        $this->configureServiceManager([
            Foo::class => new Foo,
        ]);

        $requiresFooInterface = $this->container->get(RequiresFooInterface::class);

        $this->assertInstanceOf(RequiresFooInterface::class, $requiresFooInterface);
        $this->assertInstanceOf(Foo::class, $requiresFooInterface->foo);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Missing preference for constructor parameter noTypeHint of MidnightTest\Unit\AutomaticDi\MissingTypeHint.
     */
    public function testMissingTypeHintAndNoConfig()
    {
        $this->container->get(MissingTypeHint::class);
    }

    public function testMissingTypeHintWithConfig()
    {
        $this->configureContainer([
            'classes' => [
                MissingTypeHint::class => [
                    'noTypeHint' => Foo::class,
                ],
            ],
        ]);
        $this->configureServiceManager([
            Foo::class => new Foo,
        ]);

        $missingTypeHint = $this->container->get(MissingTypeHint::class);

        $this->assertInstanceOf(MissingTypeHint::class, $missingTypeHint);
        $this->assertInstanceOf(Foo::class, $missingTypeHint->noTypeHint);
    }

    public function testNoConstructor()
    {
        $foo = $this->container->get(Foo::class);

        $this->assertInstanceOf(Foo::class, $foo);
    }

    public function testConfigureParameterDefinedInParentClass()
    {
        $this->configureContainer([
            'classes' => [
                Bar::class => [
                    'foo' => Foo::class,
                ],
            ],
        ]);
        $this->configureServiceManager([
            Foo::class => new Foo,
        ]);

        $bar = $this->container->get(Bar::class);

        $this->assertInstanceOf(Bar::class, $bar);
        /** @var Bar $bar */
        $this->assertInstanceOf(Foo::class, $bar->foo);
    }

    public function testDefaultValueIsUsed()
    {
        $object = $this->container->get(HasDefaultValue::class);

        $this->assertInstanceOf(HasDefaultValue::class, $object);
        /** @var HasDefaultValue $object */
        $this->assertSame(23, $object->value);
    }

    /**
     * @param array $config
     */
    private function configureServiceManager(array $config)
    {
        foreach ($config as $name => $service) {
            $this->serviceManager->setService($name, $service);
        }
    }

    /**
     * @param array $config
     */
    private function configureContainer(array $config)
    {
        $classPreferences = isset($config['classes']) ? $config['classes'] : [];
        $this->config->method('getClassPreferences')->willReturn($classPreferences);
        $preferences = isset($config['preferences']) ? $config['preferences'] : [];
        $this->config->method('getPreferences')->willReturn($preferences);
    }
}

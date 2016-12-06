# midnight/automatic-di

[![Build Status](https://travis-ci.org/MidnightDesign/automatic-di.svg?branch=master)](https://travis-ci.org/MidnightDesign/automatic-di)

This package provides automatic dependency injection for PHP. It requires a 
[container-interop](https://github.com/container-interop/container-interop)-compatible container. 

## Installation
Composer: [midnight/automatic-di](https://packagist.org/packages/midnight/automatic-di)

## Usage

Let's say we have the following interfaces and classes: 

```php
interface FooInterface {
}

class Foo {
    private $bar;
    public function __construct(Bar $bar) {
        $this->bar = $bar;
    }
}

class Bar {
}

interface BazInterface {
}

class Baz {
}

class Hodor {
    private $baz;
    public function __construct(BazInterface $baz) {
        $this->baz = $baz;
    }
}
```

Now we can use the `AutomaticDiContainer` like this:

```php
$wrappedContainer = new Some\Other\Container; // An implementation of Interop\Container\ContainerInterface
$config = Midnight\AutomaticDi\AutomaticDiConfig::fromArray([
    'preferences' => [
        FooInterface::class => Foo::class,
    ],
    'classes' => [
        Hodor::class => [
            'baz' => Baz::class,
        ],
    ],
]);
$container = new Midnight\AutomaticDi\AutomaticDiContainer($wrappedContainer, $config);

$foo = $container->get(FooInterface::class); // Returns an instance of Foo
$hodor = $container->get(Hodor::class); // Returns an instance of Hodor with an injected Baz
```

## Performance
> This package works via reflection. Isn't that slow?

Yes, it is. A cache is coming soon.

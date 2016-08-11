<?php declare(strict_types = 1);

namespace MidnightTest\Unit\AutomaticDi;

class Baz
{
    public $foo;

    public function __construct(FooInterface $foo)
    {
        $this->foo = $foo;
    }
}

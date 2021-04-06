<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

class RequiresFooInterface
{
    public FooInterface $foo;

    public function __construct(FooInterface $foo)
    {
        $this->foo = $foo;
    }
}

<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

class RequiresFoo
{
    public Foo $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}

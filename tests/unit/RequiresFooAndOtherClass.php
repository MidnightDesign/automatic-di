<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

final class RequiresFooAndOtherClass
{
    private Foo $foo;
    private OtherClass $other;

    public function __construct(Foo $foo, OtherClass $other)
    {
        $this->foo = $foo;
        $this->other = $other;
    }
}

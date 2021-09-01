<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

class UnionTypeWithDefaultValue
{
    public Baz|int $bazInt;

    public function __construct(Baz|int $bazInt = 42)
    {
        $this->bazInt = $bazInt;
    }
}

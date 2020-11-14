<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

class HasDefaultValue
{
    public int $value;

    public function __construct(int $value = 23)
    {
        $this->value = $value;
    }
}

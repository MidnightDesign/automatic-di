<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

class RequiresUnionType
{
    public Foo|Bar $fooBar;

    public function __construct(Foo|Bar $fooBar)
    {
        $this->fooBar = $fooBar;
    }
}

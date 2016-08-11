<?php

namespace MidnightTest\Unit\AutomaticDi;

/**
 * Class RequiresFooInterface
 *
 * @package MidnightTest\Unit\AutomaticDi
 */
class RequiresFooInterface
{
    /** @var FooInterface */
    public $foo;

    /**
     * RequiresFooInterface constructor.
     *
     * @param FooInterface $foo
     */
    public function __construct(FooInterface $foo)
    {
        $this->foo = $foo;
    }
}

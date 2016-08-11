<?php

namespace MidnightTest\Unit\AutomaticDi;

/**
 * Class RequiresFoo
 *
 * @package MidnightTest\Unit\AutomaticDi
 */
class RequiresFoo
{
    /** @var Foo */
    public $foo;

    /**
     * RequiresFoo constructor.
     *
     * @param Foo $foo
     */
    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}

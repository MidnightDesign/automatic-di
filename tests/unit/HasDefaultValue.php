<?php declare(strict_types = 1);

namespace MidnightTest\Unit\AutomaticDi;

class HasDefaultValue
{
    /** @var int */
    public $value;

    public function __construct($value = 23)
    {
        $this->value = $value;
    }
}

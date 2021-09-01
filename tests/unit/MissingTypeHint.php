<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi;

class MissingTypeHint
{
    /** @var mixed */
    public $noTypeHint;

    /**
     * @param mixed $noTypeHint
     */
    public function __construct($noTypeHint)
    {
        $this->noTypeHint = $noTypeHint;
    }
}

<?php

namespace MidnightTest\Unit\AutomaticDi;

/**
 * Class MissingTypeHint
 *
 * @package MidnightTest\Unit\AutomaticDi
 */
class MissingTypeHint
{
    /** @var mixed */
    public $noTypeHint;

    /**
     * MissingTypeHint constructor.
     *
     * @param $noTypeHint
     */
    public function __construct($noTypeHint)
    {
        $this->noTypeHint = $noTypeHint;
    }
}

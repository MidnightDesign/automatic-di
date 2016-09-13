<?php

namespace MidnightTest\Unit\AutomaticDi\TestDouble;

use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;

class NotFoundException implements NotFoundExceptionInterface
{
    public static function fromId(string $id):NotFoundExceptionInterface
    {
        return new self(sprintf('Could not find "%s".', $id));
    }
}

<?php declare(strict_types = 1);

namespace MidnightTest\Unit\AutomaticDi\TestDouble;

use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function fromId(string $id): NotFoundExceptionInterface
    {
        return new self(sprintf('Could not find "%s".', $id));
    }
}

<?php

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi\TestDouble;

use RuntimeException;

use function sprintf;

class NotFoundException extends RuntimeException
{
    public static function fromId(string $id): self
    {
        return new self(sprintf('Could not find "%s".', $id));
    }
}

<?php

declare(strict_types=1);

namespace Midnight\AutomaticDi\Cache\Exception;

use LogicException;

use function sprintf;

class CacheMissException extends LogicException
{
    public static function fromKey(string $key): CacheMissException
    {
        return new self(sprintf('Unknown entry "%s". Did you forget to call has() first?', $key));
    }
}

<?php declare(strict_types = 1);

declare(strict_types=1);

namespace MidnightTest\Unit\AutomaticDi\TestDouble;

use Psr\Container\ContainerInterface;

use function array_key_exists;

class MemoryContainer implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $services = [];

    /**
     * @param array<string, mixed> $services
     */
    public function setServices(array $services): void
    {
        $this->services = $services;
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw NotFoundException::fromId($id);
        }
        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}

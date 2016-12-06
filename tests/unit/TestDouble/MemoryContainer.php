<?php declare(strict_types = 1);

namespace MidnightTest\Unit\AutomaticDi\TestDouble;

use Interop\Container\ContainerInterface;

class MemoryContainer implements ContainerInterface
{
    /** @var array */
    private $services = [];

    public function setServices(array $services)
    {
        $this->services = $services;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw NotFoundException::fromId($id);
        }
        return $this->services[$id];
    }

    public function has($id)
    {
        return array_key_exists($id, $this->services);
    }
}

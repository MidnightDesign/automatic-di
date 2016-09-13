<?php

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
            throw \MidnightTest\Unit\AutomaticDi\TestDouble\NotFoundException::fromId($id);
        }
        return $this->services[$id];
    }

    public function has($id)
    {
        return array_key_exists($id, $this->services);
    }
}

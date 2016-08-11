<?php

namespace Midnight\AutomaticDi;

use Interop\Container\ContainerInterface;

class AutomaticDiContainerFactory
{
    public function __invoke(ContainerInterface $container):AutomaticDiContainer
    {
        return new AutomaticDiContainer($container, $container->get(AutomaticDiConfig::class));
    }
}

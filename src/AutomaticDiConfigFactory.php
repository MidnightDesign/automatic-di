<?php

namespace Midnight\AutomaticDi;

use Interop\Container\ContainerInterface;

class AutomaticDiConfigFactory
{
    public function __invoke(ContainerInterface $container):AutomaticDiConfig
    {
        return AutomaticDiConfig::fromConfig($container->get('Config')['di']);
    }
}

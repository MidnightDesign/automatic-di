<?php

namespace Midnight\AutomaticDi;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class AutomaticDiServiceFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->__invoke($serviceLocator, $requestedName);
    }

    private function getContainer(ContainerInterface $container):AutomaticDiContainer
    {
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator();
        }
        return $container->get(AutomaticDiContainer::class);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $this->getContainer($container)->has($requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return $this->getContainer($container)->get($requestedName);
    }
}

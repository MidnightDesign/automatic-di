<?php

namespace Midnight\AutomaticDi;

use Interop\Container\ContainerInterface;
use LogicException;
use ReflectionClass;
use ReflectionParameter;

class AutomaticDiContainer implements ContainerInterface
{
    /** @var ContainerInterface */
    private $container;
    /** @var AutomaticDiConfig */
    private $config;

    public function __construct(ContainerInterface $container, AutomaticDiConfig $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public function get($id)
    {
        if (interface_exists($id)) {
            return $this->container->get($this->getPreferences()[$id]);
        }
        $reflectionClass = new ReflectionClass($id);

        return $reflectionClass->newInstanceArgs($this->createConstructorArgs($reflectionClass));
    }

    public function has($id)
    {
        if (class_exists($id)) {
            return true;
        }
        if (interface_exists($id) && $this->hasPreference($id)) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    private function createConstructorArgs(ReflectionClass $reflectionClass)
    {
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return [];
        }
        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            try {
                $argument = $this->createArgument($parameter, $reflectionClass);
            } catch (LogicException $e) {
                if (!$parameter->isOptional()) {
                    throw $e;
                }
                $argument = $parameter->getDefaultValue();
            }
            $args[] = $argument;
        }
        return $args;
    }

    private function createArgument(ReflectionParameter $parameter, ReflectionClass $class)
    {
        $serviceName = $this->serviceName($parameter, $class);
        return $this->container->get($serviceName);
    }

    /**
     * @return string|null
     */
    private function serviceName(ReflectionParameter $parameter, ReflectionClass $class)
    {
        $classPreferences = $this->config->getClassPreferences();
        if (isset($classPreferences[$class->getName()][$parameter->name])) {
            return $classPreferences[$class->getName()][$parameter->name];
        }
        $class = $parameter->getClass();
        if ($class === null) {
            throw new LogicException(sprintf(
                'Missing preference for constructor parameter %s of %s.',
                $parameter->name,
                $parameter->getDeclaringClass()->name
            ));
        }
        $className = $class->name;
        return $this->getPreference($className);
    }

    /**
     * @param string $className
     * @return string
     */
    private function getPreference($className)
    {
        $preferences = $this->getPreferences();
        if (isset($preferences[$className])) {
            return $preferences[$className];
        }
        return $className;
    }

    /**
     * @return array
     */
    private function getPreferences()
    {
        return $this->config->getPreferences();
    }

    /**
     * @param string $id
     * @return bool
     */
    private function hasPreference($id)
    {
        return isset($this->getPreferences()[$id]);
    }
}

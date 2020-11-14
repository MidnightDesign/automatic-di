<?php

declare(strict_types=1);

namespace Midnight\AutomaticDi;

use Interop\Container\ContainerInterface;
use LogicException;
use ReflectionClass;
use ReflectionParameter;

use function class_exists;
use function interface_exists;
use function sprintf;

class AutomaticDiContainer implements ContainerInterface
{
    private ContainerInterface $container;
    private AutomaticDiConfig $config;

    public function __construct(ContainerInterface $container, AutomaticDiConfig $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * @param string $id
     * @return mixed|object
     * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function get($id)
    {
        if (interface_exists($id)) {
            return $this->container->get($this->getPreferences()[$id]);
        }
        // @phpstan-ignore-next-line
        $reflectionClass = new ReflectionClass($id);

        return $reflectionClass->newInstanceArgs($this->createConstructorArgs($reflectionClass));
    }

    /**
     * @param string $id
     * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function has($id): bool
    {
        if (class_exists($id)) {
            return true;
        }
        return interface_exists($id) && $this->hasPreference($id);
    }

    /**
     * @return array<int, mixed>
     */
    private function createConstructorArgs(ReflectionClass $reflectionClass): array
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

    /**
     * @return mixed
     */
    private function createArgument(ReflectionParameter $parameter, ReflectionClass $class)
    {
        $serviceName = $this->serviceName($parameter, $class);
        return $this->container->get($serviceName);
    }

    private function serviceName(ReflectionParameter $parameter, ReflectionClass $class): string
    {
        $classPreferences = $this->config->getClassPreferences();
        if (isset($classPreferences[$class->getName()][$parameter->name])) {
            return $classPreferences[$class->getName()][$parameter->name];
        }
        $class = $parameter->getClass();
        if ($class === null) {
            $declaringClass = $parameter->getDeclaringClass() !== null ? $parameter->getDeclaringClass()->name : '?';
            throw new LogicException(
                sprintf(
                    'Missing preference for constructor parameter %s of %s.',
                    $parameter->name,
                    $declaringClass
                )
            );
        }
        $className = $class->name;
        return $this->getPreference($className);
    }

    private function getPreference(string $className): string
    {
        $preferences = $this->getPreferences();
        if (isset($preferences[$className])) {
            return $preferences[$className];
        }
        return $className;
    }

    /**
     * @return array<string, string>
     */
    private function getPreferences(): array
    {
        return $this->config->getPreferences();
    }

    private function hasPreference(string $id): bool
    {
        return isset($this->getPreferences()[$id]);
    }
}

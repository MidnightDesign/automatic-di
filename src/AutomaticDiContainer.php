<?php

declare(strict_types=1);

namespace Midnight\AutomaticDi;

use LogicException;
use Psr\Container\ContainerInterface;
use Midnight\AutomaticDi\Cache\CacheInterface;
use Midnight\AutomaticDi\Cache\MemoryCache;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

use function class_exists;
use function interface_exists;
use function sprintf;

class AutomaticDiContainer implements ContainerInterface
{
    private ContainerInterface $container;
    private AutomaticDiConfig $config;
    /** @var CacheInterface */
    private $cache;

    public function __construct(ContainerInterface $container, AutomaticDiConfig $config, CacheInterface $cache = null)
    {
        $this->container = $container;
        $this->config = $config;
        $this->cache = $cache ?? new MemoryCache;
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        if (interface_exists($id)) {
            return $this->container->get($this->getPreferences()[$id]);
        }
        // @phpstan-ignore-next-line
        $reflectionClass = new ReflectionClass($id);

        return $reflectionClass->newInstanceArgs($this->createConstructorArgs($reflectionClass));
    }

    public function has(string $id): bool
    {
        if (class_exists($id)) {
            return true;
        }
        return interface_exists($id) && $this->hasPreference($id);
    }

    /**
     * @return list<mixed>
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
        $cacheKey = $this->createParameterCacheKey($parameter);
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $classPreferences = $this->config->getClassPreferences();
        if (isset($classPreferences[$class->getName()][$parameter->name])) {
            $serviceName = $classPreferences[$class->getName()][$parameter->name];
        } else {
            $type = $parameter->getType();
        if ($type instanceof ReflectionUnionType) {
            throw new LogicException(
                sprintf(
                    'Constructor parameter %s is a union type, which is not supported, yet.',
                    $parameter->name,
                )
            );
        }
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            /** @var class-string $className */
            $className = $type->getName();
            $type = new ReflectionClass($className);
        } else {
            $type = null;
        }

        if ($type === null) {
                $declaringClass = $parameter->getDeclaringClass() !== null ? $parameter->getDeclaringClass()->name : '?';throw new LogicException(sprintf(
                    'Missing preference for constructor parameter %s of %s.',
                    $parameter->name,
                    $declaringClass
                )
            );
            }
            $serviceName = $this->getPreference($className);
        }
        $this->cache->set($cacheKey, $serviceName);
        return $serviceName;
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
     * @return array<class-string, class-string>
     */
    private function getPreferences(): array
    {
        return $this->config->getPreferences();
    }

    private function hasPreference(string $id): bool
    {
        return isset($this->getPreferences()[$id]);
    }

    private function createParameterCacheKey(ReflectionParameter $parameter): string
    {
        return $parameter->getDeclaringClass()->getName() . '::' . $parameter->getName();
    }
}

<?php declare(strict_types = 1);

namespace Midnight\AutomaticDi;

use Interop\Container\ContainerInterface;
use LogicException;
use Midnight\AutomaticDi\Cache\CacheInterface;
use Midnight\AutomaticDi\Cache\MemoryCache;
use ReflectionClass;
use ReflectionParameter;

class AutomaticDiContainer implements ContainerInterface
{
    /** @var ContainerInterface */
    private $container;
    /** @var AutomaticDiConfig */
    private $config;
    /** @var CacheInterface */
    private $cache;

    public function __construct(ContainerInterface $container, AutomaticDiConfig $config, CacheInterface $cache = null)
    {
        $this->container = $container;
        $this->config = $config;
        $this->cache = $cache ?? new MemoryCache;
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
        $cacheKey = $this->createParameterCacheKey($parameter);
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $classPreferences = $this->config->getClassPreferences();
        if (isset($classPreferences[$class->getName()][$parameter->name])) {
            $serviceName = $classPreferences[$class->getName()][$parameter->name];
        } else {
            $class = $parameter->getClass();
            if ($class === null) {
                throw new LogicException(sprintf(
                    'Missing preference for constructor parameter %s of %s.',
                    $parameter->name,
                    $parameter->getDeclaringClass()->name
                ));
            }
            $className = $class->name;
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

<?php

namespace Midnight\AutomaticDi;

use InvalidArgumentException;

class AutomaticDiConfig
{
    /** @var array */
    private $preferences;
    /** @var array */
    private $classPreferences;

    private function __construct(array $preferences, array $classPreferences)
    {
        $this->preferences = $preferences;
        $this->classPreferences = $classPreferences;
    }

    public static function fromArray(array $config):AutomaticDiConfig
    {
        self::checkConfigArray($config);
        return new self($config['preferences'], $config['classes']);
    }

    private static function checkConfigArray(array $config)
    {
        foreach (['preferences', 'classes'] as $key) {
            if (!array_key_exists($key, $config) || !is_array($config[$key])) {
                throw new InvalidArgumentException(sprintf('Missing or invalid config key "%s".', $key));
            }
        }
    }

    public function getPreferences():array
    {
        return $this->preferences;
    }

    public function getClassPreferences():array
    {
        return $this->classPreferences;
    }
}

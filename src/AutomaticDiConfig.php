<?php

namespace Midnight\AutomaticDi;

use Assert\Assertion;

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

    public static function fromConfig(array $config):AutomaticDiConfig
    {
        Assertion::isArray($config['preferences']);
        Assertion::isArray($config['classes']);
        return new self($config['preferences'], $config['classes']);
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

<?php

declare(strict_types=1);

namespace Midnight\AutomaticDi;

use InvalidArgumentException;

use function array_key_exists;
use function is_array;
use function sprintf;

class AutomaticDiConfig
{
    /** @var array<class-string, class-string> */
    private array $preferences;
    /** @var array<class-string, array<string, class-string>> */
    private array $classPreferences;

    /**
     * @param array<class-string, class-string> $preferences
     * @param array<class-string, array<string, class-string>> $classPreferences
     */
    private function __construct(array $preferences, array $classPreferences)
    {
        $this->preferences = $preferences;
        $this->classPreferences = $classPreferences;
    }

    /**
     * @param array{
     *      preferences: array<class-string, class-string>,
     *      classes: array<class-string, array<string, class-string>>
     * } $config
     */
    public static function fromArray(array $config): AutomaticDiConfig
    {
        self::checkConfigArray($config);
        return new self($config['preferences'], $config['classes']);
    }

    /**
     * @param array<string, array<class-string, array<string, class-string>|class-string>> $config
     */
    private static function checkConfigArray(array $config): void
    {
        foreach (['preferences', 'classes'] as $key) {
            // @phpstan-ignore-next-line
            if (!array_key_exists($key, $config) || !is_array($config[$key])) {
                throw new InvalidArgumentException(sprintf('Missing or invalid config key "%s".', $key));
            }
        }
    }

    /**
     * @return array<string, string>
     */
    public function getPreferences(): array
    {
        return $this->preferences;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getClassPreferences(): array
    {
        return $this->classPreferences;
    }
}

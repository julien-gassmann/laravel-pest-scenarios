<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Closure;

use function Jgss\LaravelPestScenarios\databaseSetup;
use function Jgss\LaravelPestScenarios\getDatabaseSetup;

final readonly class DatabaseSetupResolver
{
    /**
     * @param  Closure(): void  $setup
     */
    public static function resolve(Closure $setup): void
    {
        $setup();
    }

    /**
     * @param  null|string|string[]|Closure(): void  $databaseSetup
     * @return Closure(): void
     */
    public static function resolveInitialContext(null|string|array|Closure $databaseSetup): Closure
    {
        return match (true) {
            is_null($databaseSetup) => fn (): null => null,
            is_string($databaseSetup) => getDatabaseSetup($databaseSetup),
            is_array($databaseSetup) => function () use ($databaseSetup): void {
                array_map(fn (string $configKey) => databaseSetup($configKey), $databaseSetup);
            },
            is_callable($databaseSetup) => $databaseSetup,
        };
    }
}

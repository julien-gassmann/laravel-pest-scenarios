<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Closure;

final readonly class DatabaseSetupResolver
{
    /**
     * @param  Closure(): void  $setup
     */
    public static function resolve(Closure $setup): void
    {
        $setup();
    }
}

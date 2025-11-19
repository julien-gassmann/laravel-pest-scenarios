<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Closure;

abstract class DatabaseSetupResolver
{
    /**
     * @param  Closure(): void  $setup
     */
    public static function resolve(Closure $setup): void
    {
        $setup();
    }
}

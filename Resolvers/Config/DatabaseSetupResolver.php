<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Config;

/**
 * Resolves pre-defined queries from configuration for test assertions.
 *
 * @extends BaseConfigResolver<void>
 */
final class DatabaseSetupResolver extends BaseConfigResolver
{
    protected static string $key = 'database_setups';
}

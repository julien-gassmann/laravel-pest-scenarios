<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Config;

/**
 * Resolves pre-defined queries from configuration for test assertions.
 *
 * @extends BaseConfigResolver<mixed>
 */
final class QueryResolver extends BaseConfigResolver
{
    protected static string $key = 'queries';
}

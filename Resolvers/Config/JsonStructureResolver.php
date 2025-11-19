<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Config;

/**
 * Resolves pre-defined JSON structures from configuration for test assertions.
 *
 * @extends BaseConfigResolver<array<array-key, mixed>|null>
 */
final class JsonStructureResolver extends BaseConfigResolver
{
    protected static string $key = 'json_structures';
}

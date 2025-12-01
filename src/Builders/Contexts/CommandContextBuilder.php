<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Jgss\LaravelPestScenarios\Definitions\Contexts\CommandContext;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\DatabaseSetupResolver;
use Mockery\MockInterface;

final readonly class CommandContextBuilder
{
    /**
     * @param  null|string|string[]|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        string $command,
        ?string $appLocale = null,
        null|string|array|Closure $databaseSetup = null,
        array $mocks = [],
    ): CommandContext {
        return new CommandContext(
            command: $command,
            appLocale: $appLocale,
            databaseSetup: DatabaseSetupResolver::resolveInitialContext($databaseSetup),
            mocks: $mocks,
        );
    }
}

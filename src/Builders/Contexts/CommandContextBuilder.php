<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Jgss\LaravelPestScenarios\Definitions\Contexts\CommandContext;
use Mockery\MockInterface;

final readonly class CommandContextBuilder
{
    /**
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        string $command,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): CommandContext {
        return new CommandContext(
            command: $command,
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ModelContext;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\ActingAsResolver;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\DatabaseSetupResolver;
use Mockery\MockInterface;

final readonly class ModelContextBuilder
{
    /**
     * @param  null|string|Closure(): ?User  $actingAs
     * @param  null|string|string[]|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        null|string|Closure $actingAs = null,
        ?string $appLocale = null,
        null|string|array|Closure $databaseSetup = null,
        array $mocks = [],
    ): ModelContext {
        return new ModelContext(
            actingAs: ActingAsResolver::resolveInitialContext($actingAs),
            appLocale: $appLocale,
            databaseSetup: DatabaseSetupResolver::resolveInitialContext($databaseSetup),
            mocks: $mocks,
        );
    }
}

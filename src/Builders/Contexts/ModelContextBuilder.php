<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ModelContext;
use Mockery\MockInterface;

final readonly class ModelContextBuilder
{
    /**
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): ModelContext {
        return new ModelContext(
            actingAs: $actingAs ?? fn () => null,
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\PolicyContext;
use Mockery\MockInterface;

class PolicyContextBuilder
{
    /**
     * @param  class-string  $policyClass
     * @param  ?Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function with(
        string $policyClass,
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): PolicyContext {
        return new PolicyContext(
            policyClass: $policyClass,
            actingAs: $actingAs ?? fn () => null,
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

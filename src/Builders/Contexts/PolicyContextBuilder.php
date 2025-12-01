<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\PolicyContext;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\ActingAsResolver;
use Mockery\MockInterface;

final readonly class PolicyContextBuilder
{
    /**
     * @param  class-string  $policyClass
     * @param  null|string|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        string $policyClass,
        null|string|Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): PolicyContext {
        return new PolicyContext(
            policyClass: $policyClass,
            actingAs: ActingAsResolver::resolveInitialContext($actingAs),
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

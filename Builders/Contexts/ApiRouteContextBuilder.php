<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Mockery\MockInterface;

class ApiRouteContextBuilder
{
    /**
     * @param  array<string, int|string|Closure(): (int|string|null)>  $routeParameters
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function with(
        string $routeName,
        array $routeParameters = [],
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): ApiRouteContext {
        return new ApiRouteContext(
            routeName: $routeName,
            routeParameters: $routeParameters,
            actingAs: $actingAs ?? fn () => null,
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

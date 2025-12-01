<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Foundation\Http\FormRequest;
use Jgss\LaravelPestScenarios\Definitions\Contexts\FormRequestContext;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\ActingAsResolver;
use Mockery\MockInterface;

final readonly class FormRequestContextBuilder
{
    /**
     * @param  class-string<FormRequest>  $formRequestClass
     * @param  array<string, int|string|Closure(): (int|string|null)>  $routeParameters
     * @param  array<string, mixed>  $payload
     * @param  null|string|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        string $formRequestClass,
        ?string $routeName = null,
        array $routeParameters = [],
        array $payload = [],
        null|string|Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): FormRequestContext {
        return new FormRequestContext(
            formRequestClass: $formRequestClass,
            routeName: $routeName,
            routeParameters: $routeParameters,
            payload: $payload,
            actingAs: ActingAsResolver::resolveInitialContext($actingAs),
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

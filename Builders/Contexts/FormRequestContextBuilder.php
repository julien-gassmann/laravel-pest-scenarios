<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Foundation\Http\FormRequest;
use Jgss\LaravelPestScenarios\Definitions\Contexts\FormRequestContext;
use Mockery\MockInterface;

class FormRequestContextBuilder
{
    /**
     * @param  class-string<FormRequest>  $formRequestClass
     * @param  array<string, int|string|Closure(): (int|string|null)>  $routeParameters
     * @param  array<string, mixed>  $payload
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function with(
        string $formRequestClass,
        ?string $routeName = null,
        array $routeParameters = [],
        array $payload = [],
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): FormRequestContext {
        return new FormRequestContext(
            formRequestClass: $formRequestClass,
            routeName: $routeName,
            routeParameters: $routeParameters,
            payload: $payload,
            actingAs: $actingAs ?? fn () => null,
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

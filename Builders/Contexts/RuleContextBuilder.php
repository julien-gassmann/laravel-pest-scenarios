<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Contracts\Validation\ValidationRule;
use Jgss\LaravelPestScenarios\Definitions\Contexts\RuleContext;
use Mockery\MockInterface;

final readonly class RuleContextBuilder
{
    /**
     * @param  class-string<ValidationRule>  $ruleClass
     * @param  array<string, mixed>  $payload
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function with(
        string $ruleClass,
        array $payload = [],
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): RuleContext {
        return new RuleContext(
            ruleClass: $ruleClass,
            payload: $payload,
            actingAs: $actingAs ?? fn () => null,
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}

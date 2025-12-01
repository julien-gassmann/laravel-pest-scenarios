<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Contracts\Validation\ValidationRule;
use Jgss\LaravelPestScenarios\Definitions\Contexts\RuleContext;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\ActingAsResolver;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\DatabaseSetupResolver;
use Mockery\MockInterface;

final readonly class RuleContextBuilder
{
    /**
     * @param  class-string<ValidationRule>  $ruleClass
     * @param  array<string, mixed>  $payload
     * @param  null|string|Closure(): ?User  $actingAs
     * @param  null|string|string[]|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        string $ruleClass,
        array $payload = [],
        null|string|Closure $actingAs = null,
        ?string $appLocale = null,
        null|string|array|Closure $databaseSetup = null,
        array $mocks = [],
    ): RuleContext {
        return new RuleContext(
            ruleClass: $ruleClass,
            payload: $payload,
            actingAs: ActingAsResolver::resolveInitialContext($actingAs),
            appLocale: $appLocale,
            databaseSetup: DatabaseSetupResolver::resolveInitialContext($databaseSetup),
            mocks: $mocks,
        );
    }
}

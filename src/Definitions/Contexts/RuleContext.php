<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Contracts\Validation\ValidationRule;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasActingAsContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasAppLocaleContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasDatabaseSetupContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasMockingContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasPayloadContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasRuleContext;
use Mockery\MockInterface;

/**
 * Immutable definition of a validation Rule context used in scenario-based tests.
 * Represents the environment in which a custom Rule is evaluated, including input data,
 * the acting user, localisation settings, and any class mocks required during execution.
 *
 * @property class-string<ValidationRule> $ruleClass Specifies the fully qualified class name of the Rule under test
 * @property array<string, mixed> $payload Provides the request input data
 * @property Closure(): ?User $actingAs Returns the user instance performing the action (e.g. fn() => User::first())
 * @property null|string $appLocale Specifies the app localisation used for the test.
 * @property Closure(): void $databaseSetup Returns the database insertions to perform before the test
 * @property array<class-string, Closure(): MockInterface> $mocks Provides classes mocked during the scenario (e.g. Filesystem::class => fn () => Mockery::mock(Filesystem::class))
 */
final readonly class RuleContext
{
    use HasActingAsContext;
    use HasAppLocaleContext;
    use HasDatabaseSetupContext;
    use HasMockingContext;
    use HasPayloadContext;
    use HasRuleContext;

    /**
     * @param  class-string<ValidationRule>|null  $ruleClass
     * @param  array<string, mixed>  $payload
     * @param  Closure(): ?User  $actingAs
     * @param  Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function __construct(
        protected ?string $ruleClass,
        protected array $payload,
        protected Closure $actingAs,
        protected ?string $appLocale,
        protected Closure $databaseSetup,
        protected array $mocks,
    ) {}

    /**
     * @param  null|class-string<ValidationRule>  $ruleClass
     * @param  null|array<string, mixed>  $payload
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  null|array<class-string, Closure(): MockInterface>  $mocks
     */
    private function replicate(
        ?string $ruleClass = null,
        ?array $payload = null,
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        ?array $mocks = null,
    ): self {
        return new self(
            ruleClass: $ruleClass ?? $this->ruleClass,
            payload: $payload ?? $this->payload,
            actingAs: $actingAs ?? $this->actingAs,
            appLocale: $appLocale ?? $this->appLocale,
            databaseSetup: $databaseSetup ?? $this->databaseSetup,
            mocks: $mocks ?? $this->mocks,
        );
    }
}

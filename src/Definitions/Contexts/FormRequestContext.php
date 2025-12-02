<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Foundation\Http\FormRequest;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasActingAsContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasAppLocaleContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasDatabaseSetupContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasFormRequestContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasMockingContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasPayloadContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasRouteContext;
use Mockery\MockInterface;

/**
 * Immutable representation of a FormRequest context used in scenario-based tests.
 * Represents the HTTP testing environment, including the route configuration,
 * request data, acting user, and the specific FormRequest being tested.
 *
 * @property class-string<FormRequest> $formRequestClass Specifies the fully qualified class name of the FormRequest under test
 * @property string|null $routeName Specifies the route name used in the scenario
 * @property array<string, int|string|callable(): (int|string|null)> $routeParameters Provides the route parameters, keyed by the parameter names (e.g. ['user' => 1])
 * @property array<string, mixed> $payload Provides the request input data
 * @property Closure(): ?User $actingAs Returns the user instance performing the request (e.g. fn() => User::first())
 * @property string|null $appLocale Specifies the app localisation used for the test.
 * @property Closure(): void $databaseSetup Returns the database insertions to perform before the test
 * @property array<class-string, Closure(): MockInterface> $mocks Provides classes mocked during the scenario (e.g. Filesystem::class => fn () => Mockery::mock(Filesystem::class))
 */
final readonly class FormRequestContext
{
    use HasActingAsContext;
    use HasAppLocaleContext;
    use HasDatabaseSetupContext;
    use HasFormRequestContext;
    use HasMockingContext;
    use HasPayloadContext;
    use HasRouteContext;

    /**
     * @param  class-string<FormRequest>  $formRequestClass
     * @param  array<string, int|string|callable(): (int|string|null)>  $routeParameters
     * @param  array<string, mixed>  $payload
     * @param  Closure(): ?User  $actingAs
     * @param  Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function __construct(
        protected string $formRequestClass,
        protected ?string $routeName,
        protected array $routeParameters,
        protected array $payload,
        protected Closure $actingAs,
        protected ?string $appLocale,
        protected Closure $databaseSetup,
        protected array $mocks,
    ) {}

    /**
     * @param  null|class-string<FormRequest>  $formRequestClass
     * @param  null|array<string, int|string|callable(): (int|string|null)>  $routeParameters
     * @param  null|array<string, mixed>  $payload
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  null|array<class-string, Closure(): MockInterface>  $mocks
     */
    private function replicate(
        ?string $formRequestClass = null,
        ?string $routeName = null,
        ?array $routeParameters = null,
        ?array $payload = null,
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        ?array $mocks = null,
    ): self {
        return new self(
            formRequestClass: $formRequestClass ?? $this->formRequestClass,
            routeName: $routeName ?? $this->routeName,
            routeParameters: $routeParameters ?? $this->routeParameters,
            payload: $payload ?? $this->payload,
            actingAs: $actingAs ?? $this->actingAs,
            appLocale: $appLocale ?? $this->appLocale,
            databaseSetup: $databaseSetup ?? $this->databaseSetup,
            mocks: $mocks ?? $this->mocks,
        );
    }
}

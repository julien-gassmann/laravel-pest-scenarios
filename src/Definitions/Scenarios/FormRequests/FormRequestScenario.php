<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\FormRequests;

use Illuminate\Contracts\Validation\Validator as ValidationContract;
use Illuminate\Support\Facades\Validator;
use Jgss\LaravelPestScenarios\Definitions\Contexts\FormRequestContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\ResolvePayload;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;

abstract readonly class FormRequestScenario
{
    use PrepareContext;
    use ResolvePayload;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $description,
        public FormRequestContext $context,
        public array $payload,
        public bool $shouldAuthorize,
    ) {}

    abstract public function defineTest(TestCallFactoryContract $factory): TestCall;

    /**
     * Executes the FormRequest's authorize() method for the current context.
     */
    public function doesFormRequestAuthorize(): bool
    {
        $formRequest = $this->context->getFormRequestInstanceWithBindings();

        return ! method_exists($formRequest, 'authorize')
            || $formRequest->authorize();
    }

    /**
     * Creates a Validator instance using the given payload and FormRequest class.
     *
     * Internally, it fakes a request using the route name and binds any model if needed,
     * so the FormRequest behaves as if it's in a real HTTP context
     * and conditional validations (based on route or model) work as expected.
     */
    public function makeValidator(): ValidationContract
    {
        $payload = $this->getResolvedPayload();

        $formRequest = $this->context
            ->withPayload($payload)
            ->getFormRequestInstanceWithBindings();

        $rules = method_exists($formRequest, 'rules') ? $formRequest->rules() : [];

        return Validator::make($payload, (array) $rules, $formRequest->messages(), $formRequest->attributes());
    }

    /**
     * @return array<string, mixed>
     */
    public function getResolvedPayload(): array
    {
        return self::resolvePayload($this->payload);
    }
}

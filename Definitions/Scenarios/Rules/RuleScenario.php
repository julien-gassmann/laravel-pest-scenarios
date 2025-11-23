<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Rules;

use Closure;
use Jgss\LaravelPestScenarios\Definitions\Contexts\RuleContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\ResolvePayload;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

abstract readonly class RuleScenario
{
    use PrepareContext;
    use ResolvePayload;

    /**
     * @param  array<int, mixed>  $parameters
     */
    public function __construct(
        public string $description,
        public RuleContext $context,
        public mixed $value,
        public array $parameters,
    ) {}

    abstract public function defineTest(TestCallFactoryContract $factory): FakeTestCall|TestCall;

    /**
     * Generates a reusable closure to simulate the validation failure callback.
     */
    public function getFailClosure(bool &$passes, ?string &$message = null): Closure
    {
        return function (string $msg) use (&$passes, &$message) {
            $passes = false;
            $message = __($msg);
        };
    }

    public function getResolvedValue(): mixed
    {
        $value = $this->value;

        return match (true) {
            is_array($value) => self::resolvePayload($value),
            is_callable($value) && is_scalar($value()) => $value(),
            default => $value,
        };
    }
}

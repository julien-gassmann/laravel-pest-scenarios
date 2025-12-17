<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Models;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ModelContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;

abstract readonly class ModelScenario
{
    use PrepareContext;

    /**
     * @param  Closure(): mixed  $input
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        public string $description,
        public ModelContext $context,
        public Closure $input,
        public array $databaseAssertions,
    ) {}

    abstract public function defineTest(TestCallFactoryContract $factory): TestCall;
}

<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Commands;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Definitions\Contexts\CommandContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;

abstract readonly class CommandScenario
{
    use PrepareContext;

    /**
     * @param  null|Closure(): string|string  $arguments
     * @param  null|Closure(PendingCommand): PendingCommand  $commandAssertions
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        public string $description,
        public CommandContext $context,
        public null|Closure|string $arguments,
        public ?Closure $commandAssertions,
        public array $databaseAssertions,
    ) {}

    abstract public function defineTest(TestCallFactoryContract $factory): TestCall;

    public function resolveArguments(): ?string
    {
        /** @var null|string $arguments */
        $arguments = is_scalar($this->arguments) || is_null($this->arguments)
            ? $this->arguments
            : ($this->arguments)();

        return $arguments;
    }
}

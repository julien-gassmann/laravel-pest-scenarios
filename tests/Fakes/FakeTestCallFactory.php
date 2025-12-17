<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Tests\Fakes;

use Closure;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;
use Pest\TestSuite;

final readonly class FakeTestCallFactory implements TestCallFactoryContract
{
    public function make(string $description, Closure $callback): TestCall
    {
        return new TestCall(new TestSuite('', ''), '', $description, $callback);
    }
}

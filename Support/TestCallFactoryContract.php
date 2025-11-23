<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Support;

use Closure;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

interface TestCallFactoryContract
{
    public function make(string $description, Closure $callback): TestCall|FakeTestCall;
}

<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Support;

use Closure;
use Pest\PendingCalls\TestCall;

interface TestCallFactoryContract
{
    public function make(string $description, Closure $callback): TestCall;
}

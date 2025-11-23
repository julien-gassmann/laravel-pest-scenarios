<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Support;

use Closure;
use Pest\PendingCalls\TestCall;

class PestTestCallFactory implements TestCallFactoryContract
{
    public function make(string $description, Closure $callback): TestCall
    {
        return it($description, $callback);
    }
}

<?php

namespace Jgss\LaravelPestScenarios\Tests\Fakes;

use Closure;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;

class FakeTestCallFactory implements TestCallFactoryContract
{
    public function make(string $description, Closure $callback): FakeTestCall
    {
        return new FakeTestCall($description, $callback);
    }
}

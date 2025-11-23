<?php

namespace Jgss\LaravelPestScenarios\Tests\Fakes;

use Closure;

final readonly class FakeTestCall
{
    public function __construct(
        protected string $description,
        protected Closure $callback,
    ) {}
}

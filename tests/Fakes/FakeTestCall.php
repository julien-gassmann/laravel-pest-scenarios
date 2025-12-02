<?php

namespace Jgss\LaravelPestScenarios\Tests\Fakes;

use Closure;

final readonly class FakeTestCall
{
    public function __construct(
        public string $description,
        public Closure $callback,
    ) {}
}

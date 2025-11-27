<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Closure;
use Mockery\MockInterface;

use function Pest\Laravel\instance;

final readonly class MockResolver
{
    /**
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public static function resolve(array $mocks): void
    {
        foreach ($mocks as $class => $mock) {
            instance($class, $mock());
        }
    }
}

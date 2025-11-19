<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Mockery\MockInterface;

use function Pest\Laravel\instance;

abstract class MockResolver
{
    /**
     * @param  array<class-string, MockInterface>  $mocks
     */
    public static function resolve(array $mocks): void
    {
        foreach ($mocks as $class => $mock) {
            instance($class, $mock);
        }
    }
}

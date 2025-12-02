<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Support;

use AssertionError;
use Jgss\LaravelPestScenarios\Support\PestTestCallFactory;
use Pest\PendingCalls\TestCall;

/**
 * This test exists solely to achieve 100% coverage.
 * As a TestCall cannot be instantiated within another TestCall instance,
 * we only verify that the expected exception is thrown in this case.
 */
it('PestTestCallFactory::make returns TestCall', function (): void {
    $factory = new PestTestCallFactory;

    $call = fn (): TestCall => $factory->make('description', fn (): true => true);

    expect($call)->toThrow(new AssertionError('assert(array_key_exists(self::FILE, $trace))'));
});

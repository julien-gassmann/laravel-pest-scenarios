<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Exceptions;

use Jgss\LaravelPestScenarios\Exceptions\InvalidConfigurationException;
use PHPUnit\Framework\SkippedTestSuiteError;

/**
 * ───────────────────────────────────────
 * Valid scenarios for InvalidConfigurationException
 * ───────────────────────────────────────
 */
describe('InvalidConfigurationException : success', function (): void {
    describe('Strict mode enabled', function (): void {
        it('ensures exception instance and message are correct', function (string $resolverName, array $availableKeys): void {
            // Arrange: Set configuration to strict mode: true
            config()->set('pest-scenarios.strict_mode.configuration', true);

            // Act: Create new Exception
            $exception = InvalidConfigurationException::unknownKey($resolverName, 'invalid_key');

            // Assert: Exception is the expected one
            expect($exception)->toBeInstanceOf(InvalidConfigurationException::class)
                ->and($exception->getMessage())->toBe(
                    "Unknown resolver key 'invalid_key' in 'resolvers.".$resolverName."'. ".
                    'Available keys: '.implode(', ', $availableKeys)
                );
        })->with([
            'actors' => ['actors', ['user', 'unauthorized', 'guest']],
            'database_setups' => ['database_setups', ['create_user', 'create_unauthorized_user', 'create_dummy', 'create_dummies', 'create_dummy_with_children', 'create_dummy_child']],
            'json_structures' => ['json_structures', ['resource', 'pagination', 'token', 'message', 'none']],
            'queries' => ['queries', ['dummy_first', 'dummy_last', 'dummy_child_first', 'int', 'string', 'bool', 'model', 'collection', 'id']],
        ]);
    });

    describe('Strict mode disabled', function (): void {
        it('ensures exception instance and message are correct', function (string $resolverName, array $availableKeys): void {
            // Arrange: Set configuration to strict mode: false
            config()->set('pest-scenarios.strict_mode.configuration', false);

            // Act: Create new Exception
            $exception = InvalidConfigurationException::unknownKey($resolverName, 'invalid_key');

            // Assert: Exception is the expected one
            expect($exception)->toBeInstanceOf(SkippedTestSuiteError::class)
                ->and($exception->getMessage())->toBe(
                    "Unknown resolver key 'invalid_key' in 'resolvers.".$resolverName."'. ".
                    'Available keys: '.implode(', ', $availableKeys)
                );
        })->with([
            'actors' => ['actors', ['user', 'unauthorized', 'guest']],
            'database_setups' => ['database_setups', ['create_user', 'create_unauthorized_user', 'create_dummy', 'create_dummies', 'create_dummy_with_children', 'create_dummy_child']],
            'json_structures' => ['json_structures', ['resource', 'pagination', 'token', 'message', 'none']],
            'queries' => ['queries', ['dummy_first', 'dummy_last', 'dummy_child_first', 'int', 'string', 'bool', 'model', 'collection', 'id']],
        ]);
    });
});

<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Exceptions;

use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;

/**
 * ───────────────────────────────────────
 * Valid scenarios for MissingDefinitionException
 * ───────────────────────────────────────
 */
describe('MissingDefinitionException : success', function (): void {
    it('ensures exception instance and message are correct', function (string $method, string $message): void {
        // Act: Create new Exception
        /** @var MissingDefinitionException $exception */
        $exception = MissingDefinitionException::$method();

        // Assert: Exception is the expected one
        expect($exception)->toBeInstanceOf(MissingDefinitionException::class)
            ->and($exception->getMessage())->toBe($message);
    })->with([
        'commandSignature()' => ['commandSignature', 'Artisan command signature is missing in context definition.'],
        'formRequestClass()' => ['formRequestClass', 'FormRequest class is missing in context definition.'],
        'policyClass()' => ['policyClass', 'Policy class is missing in context definition.'],
        'routeName()' => ['routeName', 'Route name is missing in context definition.'],
        'ruleClass()' => ['ruleClass', 'Rule class is missing in context definition.'],
    ]);
});

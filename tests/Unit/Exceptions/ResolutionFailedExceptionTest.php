<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Exceptions;

use Jgss\LaravelPestScenarios\Exceptions\ResolutionFailedException;
use PHPUnit\Framework\SkippedTestSuiteError;
use Throwable;

/**
 * ───────────────────────────────────────
 * Valid scenarios for ResolutionFailedException
 * ───────────────────────────────────────
 */
describe('ResolutionFailedException : success', function (): void {
    describe('Strict mode enabled', function (): void {
        it('ensures exception instance and message are correct', function (string $method, string $message, array $parameters): void {
            // Arrange: Set configuration to strict mode: true
            config()->set('pest-scenarios.strict_mode.resolution', true);

            // Act: Create new Exception
            /** @var Throwable $exception */
            $exception = ResolutionFailedException::$method(...$parameters);

            // Assert: Exception is the expected one
            expect($exception)->toBeInstanceOf(ResolutionFailedException::class)
                ->and($exception->getMessage())->toBe($message);
        })->with([
            'formRequestNotFound()' => ['formRequestNotFound', "Unable to find form request class : 'form_request_class'.", ['form_request_class']],
            'formRequestNotExtending()' => ['formRequestNotExtending', "Provided class 'form_request_class' doesn't extend FormRequest.", ['form_request_class']],
            'formRequestModelNotFound()' => ['formRequestModelNotFound', "Unable to find model 'field' with value 'value'.", ['field', 'value']],
            'policyClassNotFound()' => ['policyClassNotFound', "Unable to find policy class : 'policy_class'.", ['policy_class']],
            'policyMethodNotFound()' => ['policyMethodNotFound', "Unable to find method 'policy_method' in 'policy_class' class.", ['policy_class', 'policy_method']],
            'routeNameNotFound()' => ['routeNameNotFound', "Unable to find route: 'route_name'.", ['route_name']],
            'routeParametersCasting()' => ['routeParametersCasting', 'Unable to cast route parameters as string.', []],
            'routeMethodNotFound()' => ['routeMethodNotFound', "Unable to resolve HTTP method for route: 'route_name'.", ['route_name']],
            'ruleClassNotFound()' => ['ruleClassNotFound', "Unable to find rule class : 'rule_class'.", ['rule_class']],
        ]);
    });

    describe('Strict mode disabled', function (): void {
        it('ensures exception instance and message are correct', function (string $method, string $message, array $parameters): void {
            // Arrange: Set configuration to strict mode: false
            config()->set('pest-scenarios.strict_mode.resolution', false);

            // Act: Create new Exception
            /** @var Throwable $exception */
            $exception = ResolutionFailedException::$method(...$parameters);

            // Assert: Exception is the expected one
            expect($exception)->toBeInstanceOf(SkippedTestSuiteError::class)
                ->and($exception->getMessage())->toBe($message);
        })->with([
            'formRequestNotFound()' => ['formRequestNotFound', "Unable to find form request class : 'form_request_class'.", ['form_request_class']],
            'formRequestNotExtending()' => ['formRequestNotExtending', "Provided class 'form_request_class' doesn't extend FormRequest.", ['form_request_class']],
            'formRequestModelNotFound()' => ['formRequestModelNotFound', "Unable to find model 'field' with value 'value'.", ['field', 'value']],
            'policyClassNotFound()' => ['policyClassNotFound', "Unable to find policy class : 'policy_class'.", ['policy_class']],
            'policyMethodNotFound()' => ['policyMethodNotFound', "Unable to find method 'policy_method' in 'policy_class' class.", ['policy_class', 'policy_method']],
            'routeNameNotFound()' => ['routeNameNotFound', "Unable to find route: 'route_name'.", ['route_name']],
            'routeParametersCasting()' => ['routeParametersCasting', 'Unable to cast route parameters as string.', []],
            'routeMethodNotFound()' => ['routeMethodNotFound', "Unable to resolve HTTP method for route: 'route_name'.", ['route_name']],
            'ruleClassNotFound()' => ['ruleClassNotFound', "Unable to find rule class : 'rule_class'.", ['rule_class']],
        ]);
    });
});

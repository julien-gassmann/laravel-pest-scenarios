<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Mockery\MockInterface;

use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forCommand()->with(
    command: 'make:scenario',
    mocks: makeMock(Filesystem::class, function (MockInterface $mock) {
        $mock->shouldReceive('ensureDirectoryExists')->once();
        $mock->shouldReceive('put')->once();
    }),
);

/**
 * ----------------------------------------------------
 * Valid scenarios for command: make:scenario ApiRoute
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : success', function () use ($context) {
    describe('ApiRoute', function () use ($context) {
        describe('missing arguments and/or options', function () use ($context) {
            // Missing all arguments and options
            Scenario::forCommand()->valid(
                description: 'asks for scenario type, file name and route name.',
                context: $context,
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsChoice(
                        question: 'Which type of Scenario should the test file perform ?',
                        answer: 'ApiRoute',
                        answers: ['ApiRoute', 'WebRoute', 'Command', 'FormRequest', 'Rule', 'Model', 'Policy'],
                    )
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Feature/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which route do you want to test ?',
                        answer: 'api.dummies.index'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing file name and route option
            Scenario::forCommand()->valid(
                description: 'asks for scenario file name and route name.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Feature/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which route do you want to test ?',
                        answer: 'api.dummies.index'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing file name
            Scenario::forCommand()->valid(
                description: 'asks for scenario file name.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute --route=api.dummies.index',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Feature/Test'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing route option -> answer question
            Scenario::forCommand()->valid(
                description: 'asks for scenario route name.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Feature/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which route do you want to test ?',
                        answer: 'api.dummies.index'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing route option -> leave question empty
            Scenario::forCommand()->valid(
                description: "ensures 'route' option can be left empty.",
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Feature/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which route do you want to test ?',
                        answer: ''
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );
        });

        describe('command complete', function () use ($context) {
            // Complete
            Scenario::forCommand()->valid(
                description: 'creates file without asking questions.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Feature/Test --route=api.dummies.index',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Complete with flag -R
            Scenario::forCommand()->valid(
                description: "ensure 'route' option can be used with -R flag.",
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Feature/Test -R api.dummies.index',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Ignore unexpected options
            Scenario::forCommand()->valid(
                description: 'ensures unexpected option is correctly ignored.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Feature/Test -R api.dummies.index -A dummy:command',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );
        });

    });
});

/**
 * ----------------------------------------------------
 * Invalid scenarios for command: make:scenario ApiRoute
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : failure', function () use ($context) {
    describe('ApiRoute', function () use ($context) {
        describe('invalid arguments', function () use ($context) {
            // Invalid scenario type
            Scenario::forCommand()->invalid(
                description: "fails when scenario type doesn't exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'NonExistingType Feature/Test --route=web.dummies.index',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Scenario type 'NonExistingType' does not exist.")
                    ->assertFailed()
                    ->assertExitCode(1)
            );

            // Invalid file name
            Scenario::forCommand()->invalid(
                description: 'fails when file name contains invalid characters.',
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Inv@lid.filename --route=api.dummies.index',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Invalid file name: only letters, numbers and '/' are allowed.")
                    ->assertFailed()
                    ->assertExitCode(1)
            );
        });

        describe('invalid options', function () use ($context) {
            // Invalid route name (arg)
            Scenario::forCommand()->invalid(
                description: "fails when 'route' passed as option does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Feature/Test --route=non.existing.route',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Unable to find route: 'non.existing.route'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );

            // Invalid route name (prompt)
            Scenario::forCommand()->invalid(
                description: "fails when 'route' passed as prompt input does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'ApiRoute Feature/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which route do you want to test ?',
                        answer: 'non.existing.route'
                    )
                    ->expectsOutputToContain("Unable to find route: 'non.existing.route'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );
        });
    });
});

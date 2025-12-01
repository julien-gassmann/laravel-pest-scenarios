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
 * Valid scenarios for command: make:scenario Model
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : success', function () use ($context) {
    describe('Model', function () use ($context) {
        describe('missing arguments and/or options', function () use ($context) {
            // Missing all arguments and options
            Scenario::forCommand()->valid(
                description: 'asks for scenario type, file name and class name.',
                context: $context,
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsChoice(
                        question: 'Which type of Scenario should the test file perform ?',
                        answer: 'Model',
                        answers: ['ApiRoute', 'WebRoute', 'Command', 'FormRequest', 'Rule', 'Model', 'Policy'],
                    )
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Unit/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which Model class do you want to test ?',
                        answer: 'User'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing file name and class option
            Scenario::forCommand()->valid(
                description: 'asks for scenario file name and class name.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Model',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Unit/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which Model class do you want to test ?',
                        answer: 'User'
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
                arguments: 'Model --class=User',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Unit/Test'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing class option -> answer question
            Scenario::forCommand()->valid(
                description: 'asks for scenario class name.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Model Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which Model class do you want to test ?',
                        answer: 'User'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing class option -> leave question empty
            Scenario::forCommand()->valid(
                description: "ensures 'class' option can be left empty.",
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Model Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which Model class do you want to test ?',
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
                arguments: 'Model Unit/Test --class=User',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Complete with flag -C
            Scenario::forCommand()->valid(
                description: "ensure 'class' option can be used with -C flag.",
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Model Unit/Test -C User',
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
                arguments: 'Model Unit/Test -C User -A dummy:command',
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
 * Invalid scenarios for command: make:scenario model
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : failure', function () use ($context) {
    describe('Model', function () use ($context) {
        describe('invalid arguments', function () use ($context) {
            // Invalid scenario type
            Scenario::forCommand()->invalid(
                description: "fails when scenario type doesn't exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'NonExistingType Unit/Test --class=User',
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
                arguments: 'Model Inv@lid.filename --class=User',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Invalid file name: only letters, numbers and '/' are allowed.")
                    ->assertFailed()
                    ->assertExitCode(1)
            );
        });

        // Invalid class name (arg)
        describe('invalid options', function () use ($context) {
            Scenario::forCommand()->invalid(
                description: "fails when 'class' passed as option does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'Model Unit/Test --class=NonExistingClass',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Unable to find Model class for: 'NonExistingClass'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );

            // Invalid class name (prompt)
            Scenario::forCommand()->invalid(
                description: "fails when 'class' passed as prompt input does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'Model Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which Model class do you want to test ?',
                        answer: 'NonExistingClass'
                    )
                    ->expectsOutputToContain("Unable to find Model class for: 'NonExistingClass'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );
        });
    });
});

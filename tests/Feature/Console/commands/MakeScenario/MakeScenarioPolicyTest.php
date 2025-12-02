<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Mockery\MockInterface;

use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forCommand()->with(
    command: 'make:scenario',
    mocks: makeMock(Filesystem::class, function (MockInterface $mock): void {
        $mock->shouldReceive('ensureDirectoryExists')->once();
        $mock->shouldReceive('put')->once();
    }),
);

/**
 * ----------------------------------------------------
 * Valid scenarios for command: make:scenario WebRoute Policy
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : success', function () use ($context): void {
    describe('Policy', function () use ($context): void {
        describe('missing arguments and/or options', function () use ($context): void {
            // Missing all arguments and options
            Scenario::forCommand()->valid(
                description: 'asks for scenario type, file name and class name.',
                context: $context,
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsChoice(
                        question: 'Which type of Scenario should the test file perform ?',
                        answer: 'Policy',
                        answers: ['ApiRoute', 'WebRoute', 'Command', 'FormRequest', 'Rule', 'Model', 'Policy'],
                    )
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Unit/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which Policy class do you want to test ?',
                        answer: 'DummyPolicy'
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
                arguments: 'Policy',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Unit/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which Policy class do you want to test ?',
                        answer: 'DummyPolicy'
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
                arguments: 'Policy --class=DummyPolicy',
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
                arguments: 'Policy Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which Policy class do you want to test ?',
                        answer: 'DummyPolicy'
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
                arguments: 'Policy Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which Policy class do you want to test ?',
                        answer: ''
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );
        });

        describe('command complete', function () use ($context): void {
            // Complete
            Scenario::forCommand()->valid(
                description: 'creates file without asking questions.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Policy Unit/Test --class=DummyPolicy',
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
                arguments: 'Policy Unit/Test -C DummyPolicy',
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
                arguments: 'Policy Unit/Test -C DummyPolicy -A dummy:command',
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
 * Invalid scenarios for command: make:scenario Policy
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : failure', function () use ($context): void {
    describe('Policy', function () use ($context): void {
        describe('invalid arguments', function () use ($context): void {
            // Invalid scenario type
            Scenario::forCommand()->invalid(
                description: "fails when scenario type doesn't exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'NonExistingType Unit/Test --class=DummyPolicy',
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
                arguments: 'Policy Inv@lid.filename --class=DummyPolicy',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Invalid file name: only letters, numbers and '/' are allowed.")
                    ->assertFailed()
                    ->assertExitCode(1)
            );
        });

        describe('invalid options', function () use ($context): void {
            // Invalid class name (arg)
            Scenario::forCommand()->invalid(
                description: "fails when 'class' passed as option does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'Policy Unit/Test --class=NonExistingClass',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Unable to find Policy class for: 'NonExistingClass'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );

            // Invalid class name (prompt)
            Scenario::forCommand()->invalid(
                description: "fails when 'class' passed as prompt input does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'Policy Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which Policy class do you want to test ?',
                        answer: 'NonExistingClass'
                    )
                    ->expectsOutputToContain("Unable to find Policy class for: 'NonExistingClass'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );
        });
    });
});

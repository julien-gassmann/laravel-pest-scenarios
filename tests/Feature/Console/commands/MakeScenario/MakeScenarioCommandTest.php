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
 * Valid scenarios for command: make:scenario Command
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : success', function () use ($context) {
    describe('Command', function () use ($context) {
        describe('missing arguments and/or options', function () use ($context) {
            // Missing all arguments and options
            Scenario::forCommand()->valid(
                description: 'asks for scenario type, file name and command name.',
                context: $context,
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsChoice(
                        question: 'Which type of Scenario should the test file perform ?',
                        answer: 'Command',
                        answers: ['ApiRoute', 'WebRoute', 'Command', 'FormRequest', 'Rule', 'Model', 'Policy'],
                    )
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Unit/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which artisan command do you want to test ?',
                        answer: 'dummy:command'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing file name and command option
            Scenario::forCommand()->valid(
                description: 'asks for scenario file name and command name.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Command',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'What is your test file name ?',
                        answer: 'Unit/Test'
                    )
                    ->expectsQuestion(
                        question: 'Which artisan command do you want to test ?',
                        answer: 'dummy:command'
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
                arguments: 'Command --command=dummy:command',
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

            // Missing command option -> answer question
            Scenario::forCommand()->valid(
                description: 'asks for scenario command name.',
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Command Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which artisan command do you want to test ?',
                        answer: 'dummy:command'
                    )
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Missing command option -> leave question empty
            Scenario::forCommand()->valid(
                description: "ensures 'command' option can be left empty.",
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Command Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which artisan command do you want to test ?',
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
                arguments: 'Command Unit/Test --command=dummy:command',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutput('Scenario test file created successfully.')
                    ->assertSuccessful()
                    ->assertExitCode(0),
            );

            // Complete with flag -A
            Scenario::forCommand()->valid(
                description: "ensure 'command' option can be used with -A flag.",
                context: $context,
                // --- Arguments and options -------------------------------------------------
                arguments: 'Command Unit/Test -A dummy:command',
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
                arguments: 'Command Unit/Test -A dummy:command -C DummyRule',
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
 * Invalid scenarios for command: make:scenario Command
 * ----------------------------------------------------
 */
describe('Commands - make:scenario : failure', function () use ($context) {
    describe('Command', function () use ($context) {
        describe('invalid arguments', function () use ($context) {
            // Invalid scenario type
            Scenario::forCommand()->invalid(
                description: "fails when scenario type doesn't exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'NonExistingType Unit/Test --command=dummy:command',
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
                arguments: 'Command Inv@lid.filename --command=dummy:command',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Invalid file name: only letters, numbers and '/' are allowed.")
                    ->assertFailed()
                    ->assertExitCode(1)
            );
        });

        describe('invalid options', function () use ($context) {
            // Invalid command (arg)
            Scenario::forCommand()->invalid(
                description: "fails when 'command' passed as option does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'Command Unit/Test --command=invalid:command',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsOutputToContain("Unable to find artisan command: 'invalid:command'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );

            // Invalid command (prompt)
            Scenario::forCommand()->invalid(
                description: "fails when 'command' passed as prompt input does not exist.",
                context: $context->withMocks([]),
                // --- Arguments and options -------------------------------------------------
                arguments: 'Command Unit/Test',
                // --- Command assertions ----------------------------------------------------
                commandAssertions: fn (PendingCommand $command) => $command
                    ->expectsQuestion(
                        question: 'Which artisan command do you want to test ?',
                        answer: 'non:existing:command'
                    )
                    ->expectsOutputToContain("Unable to find artisan command: 'non:existing:command'.")
                    ->assertFailed()
                    ->assertExitCode(1),
            );
        });
    });
});

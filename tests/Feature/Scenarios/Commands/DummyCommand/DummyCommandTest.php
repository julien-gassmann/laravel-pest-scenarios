<?php

use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

use function Jgss\LaravelPestScenarios\actorId;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

$context = Context::forCommand()->with(
    command: 'dummy:command',
    databaseSetup: 'create_user',
);

/**
 * ----------------------------------------------------
 * Valid scenarios for command: dummy:command
 * ----------------------------------------------------
 */
describe('Commands - dummy:command : success', function () use ($context) {
    Scenario::forCommand()->valid(
        description: 'can perform database assertions.',
        context: $context,
        // --- Arguments and options -------------------------------------------------
        arguments: fn () => '--user_id='.actorId('user'),
        // --- Command assertions ----------------------------------------------------
        commandAssertions: fn (PendingCommand $command) => $command
            ->expectsOutput('Command complete.')
            ->assertSuccessful()
            ->assertExitCode(0),
        // --- Command assertions ----------------------------------------------------
        databaseAssertions: [
            fn () => assertDatabaseHas('users', [
                'id' => actorId('user'),
                'name' => 'Dummy Name',
            ]),
        ],
    );
});

/**
 * ----------------------------------------------------
 * Invalid scenarios for command: dummy:command
 * ----------------------------------------------------
 */
describe('Commands - dummy:command : failure', function () use ($context) {
    Scenario::forCommand()->invalid(
        description: 'can perform database assertions.',
        context: $context,
        // --- Arguments and options -------------------------------------------------
        arguments: '--user_id=999999',
        // --- Command assertions ----------------------------------------------------
        commandAssertions: fn (PendingCommand $command) => $command
            ->expectsOutputToContain('User not found.')
            ->assertFailed()
            ->assertExitCode(1),
        // --- Command assertions ----------------------------------------------------
        databaseAssertions: [
            fn () => assertDatabaseMissing('users', [
                'name' => 'Dummy Name',
            ]),
        ],
    );
});

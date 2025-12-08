# Commands

Command scenarios let you test your Artisan console commands, focusing on their arguments, options, input/output behavior, and interactions with dependencies.  
You can check out this [command test file](../../tests/Feature/Scenarios/Commands/DummyCommand/DummyCommandTest.php) if you want to see what a concrete usage looks like.

To quickly get started, you can generate a prefilled test file with:
```bash
php artisan make:scenario Command
```

### Minimal Example (TL;DR)

```php
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

$context = Context::forCommand()->with(
    command: 'cache:clear',
);

Scenario::forCommand()->valid(
    description: 'clears cache successfully',
    context: $context,
    commandAssertions: fn (PendingCommand $command) => $command->assertSuccessful(),
);
```

---

## How It Works

Both **Valid** and **Invalid** scenarios follow the same lifecycle:

```
Context
   ↓ with()
      - configure all shared test settings (command signature, localisation, DB setup, mocks)
Scenario
   ↓ prepareContext()
      - set up the database
      - initialize mocks
      - set app locale
   ↓ artisan()
   ↓ run command assertions
   ↓ run()
   ↓ run database assertions
```

---

## Context

The Context defines everything your scenario needs before running the command.
It centralizes all shared configuration for your command tests, including:

- The command signature
- The application locale (optional)
- Database setup steps (optional)
- Mocks to apply before each scenario (optional)

You typically declare a base Context once at the top of the file  
and let all your scenarios reuse or extend it with provided modifiers.

```php
use Illuminate\Filesystem\Filesystem;
use Jgss\LaravelPestScenarios\Context;
use Mockery;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forCommand()->with(
    command: 'make:scenario',
    
    appLocale: 'en', // Default: your app default locale
    
    databaseSetup: null, // Default: fn () => null
    
    mocks: makeMock(Filesystem::class, function (MockInterface $mock) {
        $mock->shouldReceive('ensureDirectoryExists')->once();
        $mock->shouldReceive('put')->once();
    }), // Default: []
);

// You can chain "with" modifiers to derive a new Context instance.
$newContext = $context
    ->withDatabaseSetup('create_users')
    ->withMocks([])
    ->withAppLocale('fr');
```

> [!TIP]
> `databaseSetup` accept closures, [config](../../config/pest-scenarios.php) resolver key or array of keys to run several setup steps before the test.


## Scenarios

These scenarios support:
- Full interaction testing for Artisan commands  
  (`expectsQuestion()`, `expectsChoice()`, `expectsOutput()`, etc.)
- Mocking of underlying services such as the filesystem, mailer, or notifications

> [!TIP]
> `arguments` can be wrapped into a closure to include dynamic value that will be resolved automatically (you can use provided helpers for this purpose).

### 🟢 Valid Scenarios

Valid scenarios describe successful command execution using Laravel’s built-in command testing methods.

Assertions include:
- Command assertions
- Database assertions (optional)

```php
use Illuminate\Notifications\Notification;
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actorId;
use function Jgss\LaravelPestScenarios\getActorId;
use function Jgss\LaravelPestScenarios\makeMock;
use function Pest\Laravel\assertDatabaseHas;

// Using interactive command
Scenario::forCommand()->valid(
    description: 'asks for scenario type and file name when missing all  arguments (ApiRoute test file).',
    
    context: $context,
    
    arguments: '--route=users.index', // Default: null
    
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
        ->expectsOutput('Scenario test file created successfully.')
        ->assertSuccessful()
        ->assertExitCode(0), // Default: null
);

// (With command activate:user)
// Using mocks and database assertions 
Scenario::forCommand()->valid(
    description: 'activates an existing user and sends a notification',
    
    context: $newContext->withMocks(
        makeMock(Notification::class, function (MockInterface $mock) { 
            $mock->shouldReceive('send')->once()
        })
    ),
    
    arguments: getActorId('last'), // Default: null
    
    commandAssertions: fn (PendingCommand $command) => $command
        ->expectsOutputToContain('User activated successfully.')
        ->assertExitCode(0), // Default: null
        
    databaseAssertions: [
        fn () => assertDatabaseHas('users', [
            'id' => actorId('last'),
            'is_active' => true,
        ]),
    ], // Default: []
);
```

### 🔴 Invalid Scenarios

Invalid scenarios cover failure cases caused by invalid arguments or application errors.

Assertions include:
- Command assertions
- Database assertions (optional)

```php
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Scenario;

Scenario::forCommand()->invalid(
    description: "fails when 'type' parameter does not match existing types.",
    
    context: $context,
    
    arguments: 'NotExisting Feature/Test', // Default: null
    
    commandAssertions: fn (PendingCommand $command) => $command
        ->expectsOutputToContain('Scenario type NotExisting does not exist.')
        ->assertFailed()
        ->assertExitCode(1), // Default: null
        
    databaseAssertions: [], // Default: []
);
```

---

Curious about what happens under the hood ? <br>
See [ValidCommandScenario.php](../../src/Definitions/Scenarios/Commands/ValidCommandScenario.php)
and [InvalidCommandScenario.php](../../src/Definitions/Scenarios/Commands/InvalidCommandScenario.php) for the internal Pest definitions used by this scenarios type.

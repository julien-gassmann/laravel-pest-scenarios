# Models

Model scenarios let you test your Eloquent Model classes in isolation, focusing on the methods, traits, and query scopes defined on a model.  
You can check out this [model test file](../../tests/Feature/Scenarios/Models/DummyModelTest.php) if you want to see what a concrete usage looks like.


To quickly get started, you can generate a prefilled test file with:
```bash
php artisan make:scenario Model
```

### Minimal Example (TL;DR)

```php
use App\Models\User;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

$context = Context::forModel()->with(
    databaseSetup: 'create_users',
);

Scenario::forModel()->valid(
    description: 'scope active returns only active users',
    context: $context,
    input: fn () => User::active()->count(),
    expectedOutput: fn () => User::where('is_active', true)->count(),
);
```

---

## How It Works

Both **Valid** and **Invalid** scenarios follow the same lifecycle:

```
Context
   ↓ with()
      - configure all shared test settings (user, localisation, DB setup, mocks)
Scenario
   ↓ prepareContext()
      - set up the database
      - initialize mocks
      - set app locale
   ↓ run provided input
   ↓ assert input vs output (or exception)
   ↓ run database assertions
```

---

## Context

The Context defines everything your scenario needs before running the provided input.
It centralizes all shared configuration for your model tests, including:

- The authenticated user (optional)
- The application locale (optional)
- Database setup steps (optional)
- Mocks to apply before each scenario (optional)

You typically declare a base Context once at the top of the file  
and let all your scenarios reuse or extend it with provided modifiers.

```php
use App\Service;
use Jgss\LaravelPestScenarios\Context;
use Mockery;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forModel()->with(
    actingAs: 'user', // Default: fn () => null
    
    appLocale: 'en', // Default: your app default locale
    
    databaseSetup: 'create_users', // Default: fn () => null
  
    mocks: makeMock(Service::class, fn (MockInterface $mock) => $mock->shouldHaveBeenCalled()), // Default: []
);

// You can chain "with" modifiers to derive a new Context instance.
$newContext = $context
    ->withActingAs('other')
    ->withAppLocale('fr')
    ->withDatabaseSetup(['create_user', 'create_other_user'])
    ->withMocks([]);
```

> [!TIP]
> `actingAs` and `databaseSetup` accept closures or [config](../../config/pest-scenarios.php) resolver keys.
> `databaseSetup` may also receive an array of keys to run several setup steps before the test.

---

## Scenarios

These scenarios follow a simple philosophy: you provide an input and an expected output, and the scenario verifies that the model behaves as intended.

### 🟢 Valid Scenarios

Valid scenarios describe successful Eloquent Model methods.

Assertions include:
- Provided input vs expected output comparison
- Database assertions (optional)

```php
use App\Models\User;
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actorId;
use function Jgss\LaravelPestScenarios\getActorId;
use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertDatabaseHas;

// Testing scope 'hasRole'
Scenario::forModel()->valid(
    description: "ensures scope 'hasRole' works for role 'user'",
    
    input: fn () => User::hasRole(queryId('role_user'))->get(),
    
    expectedOutput: fn () => User::whereRelation('roles', 'role_id', '=', queryId('role_user'))->get(), // Default: fn () => null
);

// Testing trait 'PerformedBy'
Scenario::forModel()->valid(
    description: "ensures trait 'PerformedBy' works when creating",
    
    input: fn () => User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ])->created_by,
    
    context: $context->actingAs('admin'),
    
    expectedOutput: getActorId('admin'), // Default: fn () => null
    
    databaseAssertions: [
        fn () => assertDatabaseHas('users', [
            'id' => actorId('last'),
            'created_by' => actorId('admin'),
        ]),
    ], // Default: []
);
```

### 🔴 Invalid Scenarios

Invalid scenarios cover failure cases caused by exceptions thrown within your model's methods.

Assertions include:
- Provided input throws expected exception
- Database assertions (optional)

```php
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actor;

Scenario::forModel()->invalid(
    description: "throws exception when assigning non-existent role",
    
    input: fn () => actor('user')->assignRole('ghost'),
    
    context: $context, // Default: Context::forModel()->with()
    
    expectedException: new InvalidArgumentException("Role 'ghost' does not exist."), // Default: fn () => null
);
```

---

Curious about what happens under the hood ? <br>
See [ValidModelScenario.php](../../src/Definitions/Scenarios/Models/ValidModelScenario.php)
and [InvalidModelScenario.php](../../src/Definitions/Scenarios/Models/InvalidModelScenario.php) for the internal Pest definitions used by this scenarios type.

# Policies

Policy scenarios let you test your Laravel Policy classes in isolation, verifying authorization logic for different users, models, and actions without performing real HTTP requests.  
You can check out this [policy test file](../../tests/Feature/Scenarios/Policies/DummyPolicyTest.php) if you want to see what a concrete usage looks like.

To quickly get started, you can generate a prefilled test file with:
```bash
php artisan make:scenario Policy
```

### Minimal Example (TL;DR)

```php
use App\Policies\UserPolicy;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

$context = Context::forPolicy()->with(
    policyClass: UserPolicy::class,
    databaseSetup: 'create_admin'
);

Scenario::forPolicy()->valid(
    description: "ensures 'before' method authorizes admin",
    context: $context->withActingAs('admin'),
    method: 'before',
);
```

---

## How It Works

Both **Valid** and **Invalid** scenarios follow the same lifecycle:

```
Context
   ↓ with()
      - configure all shared test settings (policy class, user, localisation, DB setup, mocks)
Scenario
   ↓ prepareContext()
      - set up the database
      - initialize mocks
      - set app locale
      - authenticate user
   ↓ run policy method
   ↓ compare with expected output
```

---

## Context

The Context defines everything your scenario needs before running the policy method.
It centralizes all shared configuration for your policy tests, including:

- The policy FQCN being tested
- The authenticated user (optional)
- The application locale (optional)
- Database setup steps (optional)
- Mocks to apply before each scenario (optional)

You typically declare a base Context once at the top of the file  
and let all your scenarios reuse or extend it with provided modifiers.

```php
use App\Policies\UserPolicy;
use App\Service;
use Jgss\LaravelPestScenarios\Context;
use Mockery;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forPolicy()->with(
    policyClass: UserPolicy::class,
    
    actingAs: 'user', // Default: fn () => null
    
    appLocale: 'en', // Default: your app default locale
    
    databaseSetup: ['create_user', 'create_admin'], // Default: fn () => null

    mocks: makeMock(Service::class, fn (MockInterface $mock) => $mock->shouldHaveBeenCalled()), // Default: []
);

// You can chain "with" modifiers to derive a new Context instance.
$newContext = $context
    ->withActingAs('other')
    ->withDatabaseSetup(['create_user', 'create_other_user'])
    ->withAppLocale('fr')
    ->withMocks([]);
```

> [!TIP]
> `actingAs` and `databaseSetup` accept closures or [config](../../config/pest-scenarios.php) resolver keys.
> `databaseSetup` may also receive an array of keys to run several setup steps before the test.

---

## Scenarios

These scenarios support methods returning `bool`, `Response`, or throwing exceptions, letting you cover both straightforward and advanced authorization flows.

> [!NOTE]
> The authenticated user (`Auth::user()`) is automatically passed as the first argument to the policy method.
> The parameters property should only include additional arguments (e.g. the target model).

### 🟢 Valid Scenarios

Valid scenarios describe successful Policy methods.

Assertions include:
- Method result vs expected output comparison

```php
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actor;

Scenario::forPolicy()->valid(
    description: "ensures 'view' method authorizes user for himself",
    
    context: $context,
    
    method: 'view',
    
    parameters: fn () => [actor('user')], // Default: fn () => []
    
    expectedOutput: fn () => new Response(allowed: true), // Default: fn () => true
);
```

### 🔴 Invalid Scenarios

Invalid scenarios cover failure cases, including when exception is thrown.

Assertions include:
- Method result vs expected output comparison
- Method result throws expected exception (optional)

```php
use function Jgss\LaravelPestScenarios\actor;

Scenario::forPolicy()->invalid(
    description: "ensures 'before' method rejects guest",
    
    context: $context->withActingAs('guest'),
    
    method: 'before',
    
    expectedOutput: fn () => null, // Default: fn () => false
);

Scenario::forPolicy()->invalid(
    description: "throws exception when trying to update a super-admin account",
    
    context: $context->withActingAs('admin'),
    
    method: 'update',
    
    parameters: fn () => [actor('super_admin')], // Default: fn () => []
    
    expectedException: new AuthorizationException("You cannot modify a super-admin account."), // Default: null
);
```

---

Curious about what happens under the hood ? <br>
See [ValidPolicyScenario.php](../../src/Definitions/Scenarios/Policies/ValidPolicyScenario.php)
and [InvalidPolicyScenario.php](../../src/Definitions/Scenarios/Policies/InvalidPolicyScenario.php) for the internal Pest definitions used by this scenarios type.

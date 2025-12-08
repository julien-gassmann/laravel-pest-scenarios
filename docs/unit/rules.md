# Rules

Rule scenarios let you test your custom Laravel ValidationRule classes in isolation, focusing on input and the expected validation result, without performing real HTTP requests.  
You can check out this [rule test file](../../tests/Feature/Scenarios/Rules/DummyRuleTest.php) if you want to see what a concrete usage looks like.


To quickly get started, you can generate a prefilled test file with:
```bash
php artisan make:scenario Rule
```

### Minimal Example (TL;DR)

```php
use App\Rules\UppercaseOnly;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

$context = Context::forRule()->with(
    ruleClass: UppercaseOnly::class,
);

Scenario::forRule()->valid(
    description: 'accepts uppercase string',
    context: $context,
    value: 'HELLO',
);
```

---

## How It Works

Both **Valid** and **Invalid** scenarios follow the same lifecycle:

```
Context
   ↓ with()
      - configure all shared test settings (rule class, payload, user, localisation, DB setup, mocks)
Scenario
   ↓ prepareContext()
      - set up the database
      - initialize mocks
      - set app locale
      - authenticate user
   ↓ instantiate rule class
   ↓ run rule's validate method
   ↓ assert validation passes or fails
```

---

## Context

The Context defines everything your scenario needs before running the policy method.
It centralizes all shared configuration for your validation rule tests, including:

- The rule FQCN being tested
- The request payload (optional)
- The authenticated user (optional)
- The application locale (optional)
- Database setup steps (optional)
- Mocks to apply before each scenario (optional)

You typically declare a base Context once at the top of the file  
and let all your scenarios reuse or extend it with provided modifiers.

```php
use App\Rules\CustomRule;
use App\Service;
use Mockery;
use Jgss\LaravelPestScenarios\Context;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forRule()->with(
    ruleClass: CustomRule::class,
    
    payload: ['other_field_from_request' => 'value'], // Default: []
    
    actingAs: 'user', // Default: fn () => null
    
    appLocale: 'en', // Default: your app default locale
    
    databaseSetup: 'create_user', // Default: fn () => null

    mocks: makeMock(Service::class, fn (MockInterface $mock) => $mock->shouldHaveBeenCalled()), // Default: []
);

// You can chain "with" modifiers to derive a new Context instance.
$newContext = $context
    ->withPayload(['other_field_from_request' => 'other_value'])
    ->withActingAs('other')
    ->withAppLocale('fr')
    ->withDatabaseSetup(['create_user', 'create_other_user']);
    ->withMocks([]);
```

> [!TIP]
> `actingAs` and `databaseSetup` accept closures or [config](../../config/pest-scenarios.php) resolver keys.
> `databaseSetup` may also receive an array of keys to run several setup steps before the test.

---

## Scenarios

These scenarios support:
- Simple rules
- Rules using parameters
- Rules implementing `DataAwareRule` to access other payload fields
- Rules that depend on the authenticated user

### 🟢 Valid Scenarios

Valid scenarios describe successful Rule validation.

Assertions include:
- Validation passes

```php
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\queryId;

// Rule class needing parameters
Scenario::forRule()->valid(
    description: "passes when provided value ('foo') is in allowed list ['foo', 'bar']",
    
    context: $context,
    
    value: 'foo',
    
    parameters: [['foo', 'bar']], // Default: []
);

// Rule class depending on another field from payload
Scenario::forRule()->valid(
    description: "passes when 'end_date' is after 'start_date'",
    
    context: $context->withPayload(['start_date' => '2025-01-01']),
    
    value: '2025-01-10',
);
```

### 🔴 Invalid Scenarios

Invalid scenarios cover failure cases.  
Error messages can be provided either as **raw messages** or as **translation keys** prefixed with `validation.` (recommended).

Assertions include:
- Validation fails
- Error message content

> [!NOTE]
> When using translation keys, placeholders like `:attribute` should be written literally.

```php
use Jgss\LaravelPestScenarios\Scenario;

// Rule class needing parameters
Scenario::forRule()->invalid(
    description: "fails when provided value ('boo') is in allowed list ['foo', 'bar']",
    
    context: $context,
    
    value: 'boo',
    
    errorMessage: 'validation.check_allowed_values',
    
    parameters: [['foo', 'bar']], // Default: []
);

// Rule class depending on another field from payload
Scenario::forRule()->invalid(
    description: "fails when 'end_date' is before 'start_date'",
    
    context: $context->withPayload(['start_date' => '2025-01-01']),
    
    value: '2026-02-12',
    
    errorMessage: 'validation.check_date',
);
```

---

Curious about what happens under the hood ? <br>
See [ValidRuleScenario.php](../../src/Definitions/Scenarios/Rules/ValidRuleScenario.php)
and [InvalidRuleScenario.php](../../src/Definitions/Scenarios/Rules/InvalidRuleScenario.php) for the internal Pest definitions used by this scenarios type.

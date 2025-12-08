#  Form Requests

Form Requests scenarios let you test your Laravel FormRequest classes in isolation, focusing purely on their validation and authorization logic, without involving controllers or actual HTTP calls.  
You can check out this [form request test file](../../tests/Feature/Scenarios/FormRequests/DummyRequest/UpdateDummyRequestTest.php) if you want to see what a concrete usage looks like.

To quickly get started, you can generate a prefilled test file with:
```bash
php artisan make:scenario FormRequest
```

### Minimal Example (TL;DR)

```php
use App\Http\Requests\UpdateUserRequest;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

$context = Context::forFormRequest()->with(
    formRequestClass: UpdateUserRequest::class,
);

Scenario::forFormRequest()->valid(
    description: 'validates correctly',
    context: $context,
    payload: ['name' => 'John'],
);
```

---

## How It Works

Both **Valid** and **Invalid** scenarios follow the same lifecycle:

```
Context
   ↓ with()
      - configure all shared test settings (form request class, route, params, payload, user, localisation, DB setup, mocks)
Scenario
   ↓ prepareContext()
      - set up the database
      - initialize mocks
      - set app locale
      - authenticate user
   ↓ doesFormRequestAuthorize()
      – bind route with parameters
      - bind user
      - run form request's authorize method
   ↓ assert authorize method passes or fails
   ↓ makeValidator()
      – inject payload
      – bind route with parameters
      - bind user
      – run form request validator 
   ↓ assert validator passes or fails
   ↓ assert errors content
```

---

## Context

The Context defines everything your scenario needs before the request is tested.
It centralizes all shared configuration for your form request tests, including:

- The form request FQCN being tested
- The request payload (optional)
- The route name and its parameters (optional)
- The authenticated user (optional)
- The application locale (optional)
- Database setup steps (optional)
- Mocks to apply before each scenario (optional)

You typically declare a base Context once at the top of the file  
and let all your scenarios reuse or extend it with provided modifiers.

```php
use App\Http\Requests\User\UserRequest;
use App\Service;
use Jgss\LaravelPestScenarios\Context;
use Mockery;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\getActorId;
use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forFormRequest()->with(
    formRequestClass: UserRequest::class, 
    
    routeName: 'users.update',  // Default: null
    
    routeParameters: ['user' => getActorId('user')], // Default: []
    
    payload: ['name' => 'New Name'], // Default: []
    
    actingAs: 'user', // Default: fn () => null
    
    appLocale: 'en', // Default: your app default locale
    
    databaseSetup: 'create_user', // Default: fn () => null

    mocks: makeMock(Service::class, fn (MockInterface $mock) => $mock->shouldHaveBeenCalled()), // Default: []
);

// You can chain "with" modifiers to derive a new Context instance.
$newContext = $context
    ->withRouteName('users.delete')
    ->withRouteParameters(['user' => getActorId('other')])
    ->withPayload(['name' => 'Other Name'])
    ->withActingAs('other')
    ->withAppLocale('fr')
    ->withDatabaseSetup(['create_user', 'create_other_user'])
    ->withMocks([]);
```

> [!NOTE]
> The `payload` defined in a Scenario always overrides the `payload` defined in the Context.
> This makes scenarios easier to understand, since the tested payload clearly belongs to the scenario itself.
> 
> You can still use the Context payload as a base payload and extend it in scenarios when needed:
> _example: `payload: [...$context->payload, 'overridden field' => 'value']`_

> [!TIP]
> `actingAs` and `databaseSetup` accept closures or [config](../../config/pest-scenarios.php) resolver keys.
> `databaseSetup` may also receive an array of keys to run several setup steps before the test.

---

## Scenarios

These scenarios simulate Laravel’s request lifecycle accurately, allowing you to test even complex validation logic based on:
- Current route
- Authenticated user
- Route parameter bindings
- Interdependent fields

Any validation rule (built-in, custom Rule classes, or validation objects) works identically.

> [!NOTE]
> Route parameter binding works for:
> - Built-in types (string, int) → value is passed as-is
> - Eloquent models → resolved using the route’s binding field (`{post:slug}` supported)
> - Backed enums → resolved via Enum::from($value)
> - Other classes → instantiated with new ClassName($value)

> [!TIP]
> `payload` can include dynamic values wrapped in closures that will be resolved automatically (you can use provided helpers for this purpose).  

### 🟢 Valid Scenarios

Valid scenarios describe successful form request authorization and validations.

Assertions include:
- Current user authorization
- Validation passes (for validation-based scenarios)
- No errors are returned (for validation-based scenarios)
- Original payload matches validated data (for validation-based scenarios)

```php
use Jgss\LaravelPestScenarios\Scenario;

// Testing interdependent fields 
Scenario::forFormRequest()->valid(
    description: "passes with valid 'password' and 'password_confirmation' (Password123!)",
    
    context: $context,
    
    payload: ['password' => 'Password123!', 'password_confirmation' => 'Password123!'], // Default: []
    
    shouldAuthorize: true, // Default: true
);
```

### 🔴 Invalid Scenarios

Invalid scenarios cover failure cases, including authorization and validation errors.  
Validation errors can be provided either as **raw messages** or as **translation keys** (recommended).

Assertions include:
- Current user authorization
- Validation failure (for validation-based scenarios)
- Fields triggering errors (for validation-based scenarios)
- Errors messages for each field (for validation-based scenarios)

> [!NOTE]
> When using **translation keys**, placeholders can be added using the format: `'translation.key|placeholder=value'`.
>
> _example : `'age' => ['between.numeric|min=18|max=25']`_

```php
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actor;

// Testing authorize() method based on authenticated user
Scenario::forFormRequest()->invalid(
    description: "ensures 'authorize()' fails when performed by guest",
    
    context: $context->actingAs('guest'),
    
    shouldAuthorize: false // Default: true
);

// Testing with French translation using translation keys
Scenario::forFormRequest()->invalid(
    description: "fails when 'password' is shorter than 12 characters and does not include number",
    
    context: $context->withAppLocale('fr'),
    
    payload: ['password' => 'Password!', 'password_confirmation' => 'Password!'], // Default: []
    
    expectedValidationErrors: ['password' => ['min.string|min=12', 'password.numbers']], // Default: []
);

// (Same test as previous one)
// Testing with default locale using raw messages 
Scenario::forFormRequest()->invalid(
    description: "fails when 'password' is shorter than 12 characters and does not include number",
    
    context: $context,
    
    payload: ['password' => 'Password!', 'password_confirmation' => 'Password!'], // Default: []
    
    expectedValidationErrors: ['password' => [
        'The password field must be at least 12 characters.', 
        'The password field must contain at least one number.'
    ]], // Default: []
);

// Testing interdependent fields 
Scenario::forFormRequest()->invalid(
    description: "fails when 'password_confirmation' is missing",
    
    context: $context,
    
    payload: ['password' => 'Password123!'], // Default: []
    
    expectedValidationErrors: ['password_confirmation' => ['required_with|values=password']], // Default: []
);
```

---

Curious about what happens under the hood ? <br>
See [ValidFormRequestScenario.php](../../src/Definitions/Scenarios/FormRequests/ValidFormRequestScenario.php)
and [InvalidFormRequestScenario.php](../../src/Definitions/Scenarios/FormRequests/InvalidFormRequestScenario.php) for the internal Pest definitions used by this scenarios type.

# API Routes

API route scenarios let you test your Laravel endpoints end-to-end cleanly.  
You can check out this [API route test file](../../tests/Feature/Scenarios/ApiRoutes/UpdateApiRouteTest.php) if you want to see what a concrete usage looks like.

To quickly get started, you can generate a prefilled test file with:
```bash
php artisan make:scenario ApiRoute
```

### Minimal Example (TL;DR)

```php
use App\Http\Models\User;
use App\Http\Resources\UserResource;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

$context = Context::forApiRoute()->with(
    routeName: 'users.index',
);

Scenario::forApiRoute()->valid(
    description: 'get all users',
    context: $context,
    expectedResponse: fn () => UserResource::collection(User::all())->response(),
);
```

---

## How It Works

Both **Valid** and **Invalid** scenarios follow the same lifecycle:

```
Context
   ↓ with()
      - configure all shared test settings (route, params, user, localisation, DB setup, mocks)
Scenario
   ↓ prepareContext()
      - set up the database
      - initialize mocks
      - set app locale
      - authenticate user
   ↓ resolveRoute()
      – get route by name
      – determine method (GET/POST/PUT/PATCH/DELETE)
      – replace URI parameters
      – convert GET payload to query string
   ↓ sendRequest()
   ↓ assert response status/structure/content
   ↓ run database assertions
```

---

## Context

The Context defines everything your scenario needs before the request is sent.
It centralizes all shared configuration for your API route tests, including:

- The route name and its parameters
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
use function Jgss\LaravelPestScenarios\getActorId;
use function Jgss\LaravelPestScenarios\makeMock;

$context = Context::forApiRoute()->with(
    routeName: 'users.update',
    
    routeParameters: ['user' => getActorId('user')], // Default: []
    
    actingAs: 'user', // Default: fn () => null
    
    appLocale: 'en', // Default: your app default locale
    
    databaseSetup: 'create_user', // Default: fn () => null
    
    mocks: makeMock(Service::class, fn (MockInterface $mock) => $mock->shouldHaveBeenCalled()), // Default: []
);

// You can chain "with" modifiers to derive a new Context instance.
$newContext = $context
    ->withRouteName('users.delete')
    ->withRouteParameters(['user' => getActorId('other')])
    ->withRoute('users.delete', ['user' => getActorId('other')]) // Shortcut to combine 'withRouteName' and 'withRouteParameters'
    ->withActingAs('other');
    ->withAppLocale('fr')
    ->withDatabaseSetup(['create_user', 'create_other_user'])
    ->withMocks([]);
```

> [!TIP]
> `actingAs` and `databaseSetup` accept closures or [config](../../config/pest-scenarios.php) resolver keys.
> `databaseSetup` may also receive an array of keys to run several setup steps before the test.

---

## Scenarios

These scenarios focus only on what matters. 
Everything else (route resolution, auth, payload formatting…) is handled automatically.

> [!TIP]
> `payload` can include dynamic values wrapped in closures that will be resolved automatically (you can use provided helpers for this purpose).

### 🟢 Valid Scenarios

Valid scenarios describe successful API interactions.

Assertions include:
- Status code
- JSON structure
- Exact response (optional)
- Database assertions (optional)

```php
use App\Http\Resources\UserResource;
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\actorId;
use function Pest\Laravel\assertDatabaseHas;

Scenario::forApiRoute()->valid(
    description: "returns 200 when user updates his own 'name' and 'email'",
    
    context: $context,
    
    payload: ['name' => 'New Name', 'email' => 'new@mail.com'], // Default: []
    
    expectedStatusCode: 200, // Default: 200
    
    expectedStructure: 'resource', // Default: ['data']
    
    expectedResponse: fn () => UserResource::make(actor('user'))->response(), // Default: null
    
    databaseAssertions: [
        fn () => assertDatabaseHas('users', [
            'id' => actorId('user'),
            'name' => 'New Name',
            'email' => 'new@mail.com',
            'updated_by' => actorId('user'),
        ]),
    ], // Default: []
);
```

> [!TIP]
> `expectedStructure` accepts either a raw value or a [config](../../config/pest-scenarios.php) resolver key.


### 🔴 Invalid Scenarios

Invalid scenarios cover failure cases such as validation errors or domain exceptions.

Assertions include:
- Status code
- JSON error structure (for validation-based failures)
- Error message (for exception-based failures)
- Database assertions (optional)

```php
use Jgss\LaravelPestScenarios\Scenario;
use function Pest\Laravel\assertDatabaseMissing;

// Invalid Scenario - based on exception failure
Scenario::forApiRoute()->invalid(
    description: 'returns 404 when updating non-existent id',
   
    context: $context->withRouteParameters(['user' => '999999']),
   
    expectedStatusCode: 404, // Default: 422
    
    expectedErrorMessage: "User '999999' not found.", // Default: null
);

// Invalid Scenario - based on validation failure
Scenario::forApiRoute()->invalid(
    description: "returns 422 when 'email' is not a valid email",
    
    context: $context,
    
    payload: ['email' => 'not_an_email'], // Default: []
    
    expectedErrorStructure: ['errors' => ['email']], // Default: []
    
    databaseAssertions: [
        fn () => assertDatabaseMissing('users', [
            'id' => actorId('user'),
            'name' => 'New Name',
            'email' => 'new@mail.com',
            'updated_by' => actorId('user'),
        ]),
    ], // Default: []
);
```

---

Curious about what happens under the hood ? <br>
See [ValidApiRouteScenario.php](../../src/Definitions/Scenarios/ApiRoutes/ValidApiRouteScenario.php)
and [InvalidApiRouteScenario.php](../../src/Definitions/Scenarios/ApiRoutes/InvalidApiRouteScenario.php) for the internal Pest definitions used by this scenarios type.
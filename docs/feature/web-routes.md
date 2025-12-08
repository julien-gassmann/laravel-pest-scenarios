# Web Routes

Web route scenarios let you test your Laravel routes that return rendered HTML views or redirects, including those triggered by validation errors.  
You can check out this [web route test file](../../tests/Feature/Scenarios/WebRoutes/UpdateWebRouteTest.php) if you want to see what a concrete usage looks like.

To quickly get started, you can generate a prefilled test file with:
```bash
php artisan make:scenario WebRoute
```

### Minimal Example (TL;DR)

```php
use App\Http\Models\User;
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

$context = Context::forWebRoute()->with(
    routeName: 'users.index',
);

Scenario::forWebRoute()->valid(
    description: 'displays all users',
    context: $context,
    responseAssertions: [
        fn (TestResponse $res) => $res
            ->assertSee('Users List')
            ->assertViewHas('users', User::all()),
    ],
);
```


---

## How It Works

Both **Valid** and **Invalid** scenarios follow the same lifecycle:

```
Context
   ↓ with()
      - configure all shared test settings (route, params, from route, from route params, user, localisation, DB setup, mocks)
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
      - set originating route
   ↓ sendRequest()
   ↓ assert response status/content/view/redirects
   ↓ run database assertions
```

---

## Context

The Context defines everything your scenario needs before the request is sent.
It centralizes all shared configuration for your web route tests, including:

- The route name and its parameters
- The originating route (for redirects) and parameters (optional)
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

$context = Context::forWebRoute()->with(
    routeName: 'users.update',
    
    routeParameters: ['user' => getActorId('user')], // Default: []
    
    fromRouteName: 'users.edit', // Default: routeName
    
    fromRouteParameters: ['user' => getActorId('user')], // Default: routeParameters
    
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
    ->withFromRouteName('users.edit')
    ->withFromRouteParameters(['user' => getActorId('other')])
    ->withFromRoute('users.edit', ['user' => getActorId('other')]) // Shortcut to combine 'withFromRouteName' and 'withFromRouteParameters'
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

It allows you to:
- Decide whether to follow redirects or only assert the initial response (`shouldFollowRedirect`).
- Check view content, session state, authentication, and other backend-rendered conditions (`responseAssertions`).

> [!TIP]
> `payload` can include dynamic values wrapped in closures that will be resolved automatically (you can use provided helpers for this purpose).

### 🟢 Valid Scenarios

Valid scenarios describe successful web interactions.

Assertions include:
- Response status
- View content or session state (optional)
- Database assertions (optional)

```php
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\actorId;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseHas;

Scenario::forWebRoute()->valid(
    description: "returns 200 when user updates his own 'name' and 'email'",
    
    context: $context,
    
    payload: ['name' => 'New Name', 'email' => 'new@mail.com'], // Default: []
    
    shouldFollowRedirect: false, // Default: false
    
    expectedStatusCode: 200, // Default: 200
    
    responseAssertions: [
        fn (TestResponse $res) => $res
            ->assertDontSee('John Doe')
            ->assertSee('New Name')
            ->assertViewHas('user', actor('user')),
    ], // Default: []
    
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


### 🔴 Invalid Scenarios

Invalid scenarios cover failure cases, including validation errors and redirects.

Assertions include:
- Response status
- View content or session state (optional)
- Database assertions (optional)

```php
use Illuminate\Support\ViewErrorBag;
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\actorId;
use function Pest\Laravel\assertDatabaseMissing;

// Without redirect
Scenario::forWebRoute()->invalid(
    description: "returns 422 with redirect when 'email' is invalid",
    
    context: $context,
    
    payload: ['email' => 'not_an_email'], // Default: []
    
    responseAssertions: [
        fn ($res) => $res
            ->assertRedirectToRoute('users.edit', ['user' => actorId('user')]),
    ], // Default: []
    
    databaseAssertions: [
        fn () => assertDatabaseMissing('users', [
            'id' => actorId('user'),
            'email' => 'not_an_email',
        ]),
    ], // Default: []
);

// With redirect
Scenario::forWebRoute()->invalid(
    description: "redirects to 'users.edit' with corresponding errors when 'email' is invalid",
    
    context: $context,
    
    payload: ['email' => 'not_an_email'], // Default: []
    
    shouldFollowRedirect: true, // Default: false
    
    expectedStatusCode: 200, // Default: 302
    
    responseAssertions: [
        fn ($res) => $res
            ->assertDontSee('not_an_email')
            ->assertViewHas(
                'errors',
                fn (ViewErrorBag $errors) => $errors->any()
                    && $errors->has('email')
            ),
    ], // Default: []
    
    databaseAssertions: [
        fn () => assertDatabaseMissing('users', [
            'id' => actorId('user'),
            'email' => 'not_an_email',
        ]),
    ], // Default: []
);
```

> [!IMPORTANT]
> Browser-based or JavaScript-rendered content is not supported yet.
> The `responseAssertions` property currently works only with backend-rendered views (Blade, simple HTML, etc.).

---

Curious about what happens under the hood ? <br>
See [ValidWebRouteScenario.php](../../src/Definitions/Scenarios/WebRoutes/ValidWebRouteScenario.php)
and [InvalidWebRouteScenario.php](../../src/Definitions/Scenarios/WebRoutes/InvalidWebRouteScenario.php) for the internal Pest definitions used by this scenarios type.
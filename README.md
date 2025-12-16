# Laravel Pest Scenarios
> Declarative, consistent and reusable test scenarios for Laravel + Pest.

[![Tests](https://img.shields.io/github/actions/workflow/status/julien-gassmann/laravel-pest-scenarios/tests.yml?label=Tests)](https://github.com/julien-gassmann/laravel-pest-scenarios/actions/workflows/tests.yml)
![Coverage](https://img.shields.io/badge/coverage-100%25-success)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/Jgss\LaravelPestScenarios/laravel-pest-scenarios.svg)](https://packagist.org/packages/Jgss\LaravelPestScenarios/laravel-pest-scenarios)
[![Total Downloads](https://img.shields.io/packagist/dt/Jgss\LaravelPestScenarios/laravel-pest-scenarios.svg)](https://packagist.org/packages/Jgss\LaravelPestScenarios/laravel-pest-scenarios)
[![License: MIT](https://img.shields.io/badge/License-MIT-brightgreen.svg)](https://opensource.org/licenses/MIT)
---

## Introducing laravel-pest-scenarios

A lightweight layer on top of Pest that makes your Laravel tests **clear**, **declarative**, and **uniform** across your entire codebase.

Instead of rewriting setup logic and repeating the same assertions in every test, this package lets you define **Contexts** (shared setup) and **Scenarios** (declarative test cases) that are reusable across your test suite.  
This way, you can focus on **what** should happen in your tests rather than **how** to implement them.

It comes with several prebuilt scenario types for both **feature** and **unit** tests (with [more to come](#contributing--roadmap)):
- **Feature tests**:
    - [API routes](docs/feature/api-routes.md) → full HTTP endpoint
    - [Web routes](docs/feature/web-routes.md) → full browser-oriented route
- **Unit tests**:
    - [Commands](docs/unit/commands.md) → Artisan scenario testing
    - [FormRequests](docs/unit/form-requests) → authorization + validation
    - [Models](docs/unit/model.md) → methods, scopes, traits
    - [Policies](docs/unit/policies.md) → authorization only
    - [Rules](docs/unit/rules.md) → validator logic only

You also get a set of globally available [helpers](#helpers) (actors, database setups, queries, mocks, JSON structures), making your tests even cleaner and more consistent.

> [!NOTE]
> This package covers roughly 80–90% of common Laravel test scenarios.
> For complex multistep logic (e.g., updating a password with multiple dependent checks), standard Pest tests may still be necessary.

---

## Why Use It?

**For Test Beginners**
- **Guided structure**: Clear test templates with an opinionated layout.
- **Blank-file syndrome**: Predefined scenario types, so you never start from nothing.
- **Fill-in-the-blanks approach**: Tests are already written, you only provide the missing pieces.
- **Progressive learning**: Start simple, then expand with database checks, mocks, etc.

**For All Teams**
- **Less boilerplate**: Focus on testing behavior, not setup.
- **Consistency**: Uniform style across tests and developers.
- **Reusability**: Define contexts and helpers once, reuse anywhere.
- **Readability**: Tests are concise and descriptive.

---

## Installation

This package requires :
- Laravel 11 > 12
- Pest 3: PHP 8.3 > 8.4
- Pest 4: PHP 8.3 > 8.5
- Named routes (for most scenarios)

```bash
composer require --dev jgss/laravel-pest-scenarios
```

---

## Configuration

This package is fully configurable via a dedicated config file.

```bash
php artisan vendor:publish --tag=pest-scenarios
```
The configuration allows you to:
- Control how strict the test suite should be (fail vs skip)
- Define reusable resolvers (actors, database setups, queries, JSON structures)

Detailed explanations are provided directly inside the published configuration file:
[`config/pest-scenarios.php`](config/pest-scenarios.php).

---

## Core Concepts

### Contexts

A Context stores all shared data for your scenarios: route infos, authenticated users, database setup, mocks, etc.  
They are **immutable**. Modifier methods prefixed with `with` let you tweak a context safely for a specific scenario.

```php
use App\Models\User;
use Illuminate\Notifications\Notification;
use Jgss\LaravelPestScenarios\Context;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\getActorId;
use function Jgss\LaravelPestScenarios\makeMock;

// Define your context once at the top of your test file
$context = Context::forApiRoute()->with(
    // --- Route infos -------------------------------------------------------------------------
    routeName: 'users.update',
    routeParameters: ['user' => getActorId('user')],
    // --- Authenticated user ------------------------------------------------------------------
    actingAs: 'admin',
    // --- Database setup ----------------------------------------------------------------------
    databaseSetup: ['create_user', 'create_admin'],
    // --- Mocked classes ----------------------------------------------------------------------
    mocks: makeMock(Notification::class, fn (MockInterface $mock) => $mock->shouldReceive('send')->once()),
);
```

### Scenarios

A Scenario defines a single declarative test case built on top of a context.
You can define valid and invalid variants to separate success and failure cases clearly.

To help you understand what this package brings on top of Pest, here’s a small comparison between a typical Pest test and the same test expressed through scenarios.

#### 🟢 Valid Scenarios:

```php
use App\Http\Resources\UserResource;
use Jgss\LaravelPestScenarios\Scenario;
use Illuminate\Notifications\Notification;
use Mockery\MockInterface;
use function Pest\Laravel\assertDatabaseHas;

// Using native Pest
it("returns 200 when admin updates user's profile", function () {
     // Arrange: Create user and admin
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Arrange: Mock notifications
    mock(Notification::class, function (MockInterface $mock) {
        $mock->shouldReceive('send')->once();
    });

    // Act: Send request with payload
    $payload = ['name' => 'New Name', 'email' => 'new@mail.com'];
    $response = actingAs($admin)
        ->patchJson("/users/{$user->id}", $payload);
  
    // Assert: Check status code and JSON structure
    $response->assertStatus(200)
        ->assertJsonStructure(['data']);

    // Assert: Check if response contains the expected resource
    $expectedResponse = UserResource::make($user->refresh())->response();
    expect($response->json())->toEqual($expectedResponse);
  
    // Assert: Check new row insertion in database
    assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'new@mail.com',
        'updated_by' => $admin->id,
    ]);
});

// Using laravel-pest-scenarios
Scenario::forApiRoute()->valid(
    description: "returns 200 when admin updates user's profile",
    // --- Context ----------------------------------------------------------------------
    context: $context,
    // --- Payload ----------------------------------------------------------------------
    payload: ['name' => 'New Name', 'email' => 'new@mail.com'],
    // --- Expected response ------------------------------------------------------------
    expectedResponse: fn () => UserResource::make(actor('user'))->response(),
    // --- Database assertions ----------------------------------------------------------
    databaseAssertions: [
        fn () => assertDatabaseHas('users', [
            'id' => actorId('user'),
            'name' => 'New Name',
            'email' => 'new@mail.com',
            'updated_by' => actorId('admin'),
        ]),
    ]
);
```

#### 🔴 Invalid Scenarios:

```php
use App\Models\User;
use Jgss\LaravelPestScenarios\Scenario;

// Using native Pest
it("returns 404 when updating non-existent id", function () {
    // Arrange: Create admin
    $admin = User::factory()->create();

    // Act: Send request with payload
    $payload = ['name' => 'New Name', 'email' => 'new@mail.com'];
    $response = actingAs($admin)
        ->patchJson('/users/999999', $payload);

    // Assert: Check status code and JSON content
    $response
        ->assertStatus(404)
        ->assertJson(['message' => "User '999999' not found."]);
});

// Using laravel-pest-scenarios
Scenario::forApiRoute()->invalid(
    description: 'returns 404 when updating non-existent id',
    // --- Context --------------------------------------------------------------------
    context: $context->withRouteParameters(['user' => '999999']),
    // --- Status code ----------------------------------------------------------------
    expectedStatusCode: 404,
    // --- Error message --------------------------------------------------------------
    expectedErrorMessage: "User '999999' not found.",
);
```

> [!NOTE]
> The `->valid()` and `->invalid()` methods generate Pest test definitions using `it()`, so you can chain modifiers like `->skip()` or `->only()` for flexible test control.  
> They can also be wrapped inside `describe()` blocks to organize tests hierarchically.

---

## Helpers

To make your tests faster, cleaner, and more maintainable, this package provides a set of **reusable helpers** based on **configurable keys** defined in your [config](config/pest-scenarios.php).

Think of them as ready-made building blocks: instead of repeating setup code, database queries, or mocks, you can just reference a helper. This keeps your tests **concise**, **readable**, and **easy to maintain**, even in large projects.

Here’s what you get out of the box:

- [Actors](docs/helpers/actors.md) → perfect for authentication and user-specific scenarios.
- [Database Setups](docs/helpers/database-setups.md) → populate tables without repeating factories everywhere.
- [Queries](docs/helpers/queries.md) – centralize frequent queries for consistent and reusable data access.
- [JSON Structures](docs/helpers/json-structures.md) → easily validate API responses without verbose arrays.
- [Mock Factory](docs/helpers/mocks.md) – simplify mock definitions by generating the exact structure required by Contexts.

> [!TIP]
> These helpers are particularly powerful when combined with **Contexts** and **Scenarios**, letting you define once and reuse everywhere. 
> But they are still available in native Pest tests when needed.

--- 

## Quick Start (5 minutes)

Get started with a working test in just a few minutes:

### Step 1: Configure your actors and database
```php
// config/pest-scenarios.php
'actors' => [
    'admin' => fn () => User::where('role', 'admin')->firstOrFail(),
],
'database_setups' => [
    'create_admin' => fn () => User::factory()->create(['role' => 'admin']),
],
```

### Step 2: Generate a test file
```bash
php artisan make:scenario ApiRoute Feature/Api/UserIndexTest
› Q: "Which route do you want to test?"
› A: "users.index"
```

### Step 3: Create your first Scenario
```php
// tests/Feature/Api/UserIndexTest.php -> valid scenarios section
Scenario::forApiRoute()->valid(
    description: 'returns 200',    
    context: $context
        ->withDatabaseSetup('create_admin') // Fill your database when not using seeders
        ->withActingAs('admin') // Set actingAs() user if route needs authentication
)
```

This is a minimal scenario asserting a status 200 (default value for API route valid scenario's `expectedStatusCode` property).   
You can add more valid or invalid scenarios to fully cover the route.

### Step 4: Run your tests
```bash
php artisan test
```

That's it! You now have a structured test file ready to customize.

---

## Contributing & Roadmap

Contributions are welcome! Run the test suite before pushing:

```bash
composer check
```

Future improvements: 
- [ ] Multistep scenario support
- [ ] Dataset utilities for scenarios
- [ ] MCP server
- [ ] Custom scenarios
- [ ] Browser-testing compatibility
- [ ] Scenarios for: Actions, Middlewares, Events, Jobs and Notifications

--- 

## Support / Contact

Maintained by  [J.G.](https://github.com/julien-gassmann)
If you find this package useful, feel free to star ⭐ the repo or share feedback!

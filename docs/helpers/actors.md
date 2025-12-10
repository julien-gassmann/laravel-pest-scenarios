# Helper: Actors

Actor helpers let you retrieve a specific user by assigning a unique key to a query.  
This is fully configurable in your config file [pest-scenarios.php](../../config/pest-scenarios.php).

### Minimal Example (TL;DR)

```php
use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\actorId;

actor('admin');     // Returns resolved admin instance
actorId('user');    // Returns resolved user's ID
```

---

## How It Works

Actors are resolved from the `resolvers.actors` section of your configuration.  
Each actor key maps to a closure that returns a model instance or `null`.

Once resolved, an actor can be:
- Returned as a model (`actor('user')`)
- Returned as a lazy closure (`getActor('user')`)
- Used for authentication via scenario contexts (`withActingAs('admin')`)


> [!NOTE]
> When used for authentication, the resolved actor is passed directly to Pest’s `actingAs()` method during scenario execution.

---

## Configuration

Actors are defined in the `resolvers.actors` array in your `config/pest-scenarios.php` file:

```php
use Tests\Queries\ActorQueries; // Not provided

'resolvers' => [
    // ...
    'actors' => [
        'user' => fn () => ActorQueries::user(),
        'admin' => fn () => ActorQueries::admin(),
        'other' => fn () => ActorQueries::other(),
        'last' => fn () => ActorQueries::last(),
        'guest' => fn () => null,
        // ...
    ],
    // ...
]
```

---

## Available Functions

The following helper functions are available:

- `actor(string $name): ?User`
- `actorId(string $name): ?int`
- `getActor(string $name): Closure(): ?User`
- `getActorId(string $name): Closure(): ?int`

> [!TIP]
> `getActor()` and `getActorId()` are useful for lazy evaluation when building dynamic parameters.

### Basic Usage

Authenticate a scenario with a specific actor:

```php
use Jgss\LaravelPestScenarios\Scenario;

Scenario::forPolicy()->valid(
    // ...
    context: $context->withActingAs('admin'),
    // ...
);
```

### Advanced Usage

Reuse an actor as a parameter:

```php
use function Jgss\LaravelPestScenarios\getActorId;
use Jgss\LaravelPestScenarios\Scenario;

Scenario::forApiRoute()->valid(
    // ...
    routeParameters: ['user' => getActorId('admin')],
    // ...
);
```


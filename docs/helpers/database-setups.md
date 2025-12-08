# Helper: Database Setups

Database setup helpers let you prepare the database for a scenario using queries referenced by a unique key.  
This is fully configurable in your config file [pest-scenarios.php](../../config/pest-scenarios.php).

### Minimal Example (TL;DR)

```php
use function Jgss\LaravelPestScenarios\databaseSetup;

databaseSetup('create_user');    // Runs the setup query (ex: insert a user)
```

---

## How It Works

Database setups are resolved from the `resolvers.database_setups` section of your configuration.  
Each database setup key maps to a closure that returns the query to perform.

Once resolved, a database setup can be:
- Executed immediately  (`databaseSetup('create_user')`)
- Returned as a lazy closure (`getDatabaseSetup('create_user')`)

> [!NOTE]
> These helpers are ideal for reusing recurring database setup insertions,
> especially when running parallel tests or when no global seeding is available.

---

## Configuration

Database setups are defined in the `resolvers.database_setups` array in your `config/pest-scenarios.php` file:

```php
use App\Model\Post;
use App\Model\User;

'resolvers' => [
    // ...
    'database_setups' => [            
        'create_user' => fn () => User::factory()->create(),
        'create_post' => fn () => Post::factory()->create(),
        'create_posts' => fn () => Post::factory(10)->create(),
        // ...
    ],
    // ...
]
```

---

## Available Functions

The following helper functions are available:

- `databaseSetup(string $name): void`
- `getDatabaseSetup(string $name): Closure(): void`

> [!TIP]
> For convenience, all context's `databaseSetup` properties accepts:
> - Single key as a `string` for simple setups
> - Array of keys as `array` for multiple queries
> - Raw closure as `Closure` for fully personalized setup

### Basic Usage

Prepare database before a scenario with a specific setup:

```php
use Jgss\LaravelPestScenarios\Scenario;

Scenario::forApiRoute()->valid(
    // ...
    context: $context->withDatabaseSetup('create_admin'),
    // ...
);
```

### Advanced Usage

Using multiple queries for specific scenario:

```php
use Jgss\LaravelPestScenarios\Scenario;

Scenario::forApiRoute()->valid(
    // ...
    context: $context->withDatabaseSetup(['create_user', 'create_admin']),
    // ...
);
```

Using a raw closure:

```php
use Jgss\LaravelPestScenarios\Scenario;

Scenario::forApiRoute()->valid(
    // ...
    context: $context->withDatabaseSetup(function () {
        // custom setup logic (ex: seed an uncommon relation)
    }),
    // ...
);
```

Using in a native Pest test:

```php
use function Jgss\LaravelPestScenarios\databaseSetup;

it('ensures whatever', function () {
    databaseSetup('create_user');
    
    // your test here, with the database filled 
})
```
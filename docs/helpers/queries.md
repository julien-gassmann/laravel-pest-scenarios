## Helper: Queries

Query helpers let you reuse specific database queries by referencing them with a unique key.  
This is fully configurable in your config file [pest-scenarios.php](../../config/pest-scenarios.php).

### Minimal Example (TL;DR)

```php
use function Jgss\LaravelPestScenarios\query;

query('post_first');      // Returns resolved Post model instance
```

---

## How It Works

Queries are resolved from the `resolvers.queries` section of your configuration.  
Each key maps to a closure that executes a query and returns its result.

Once resolved, a query can be:
- Executed immediately  (`query('post_first')`)
- Returned as a lazy closure (`getQuery('post_first')`)

> [!NOTE]
> Query helpers are especially useful when you:  
> - Reuse the same database query across multiple tests
> - Work with dynamic data that changes on every run (factories, parallel testing)
> - Want clean and concise scenario definitions
>
> However, they are not meant to centralize all queries.
> Use them when they simplify your test code — avoid them when they make things harder to read.

---

## Configuration

Database setups are defined in the `resolvers.database_setups` array in your `config/pest-scenarios.php` file:

```php
use App\Model\Post;

'resolvers' => [
    // ...
    'queries' => [
        'post_first'      => fn () => Post::first(),
        'post_count'      => fn () => Post::count(),
        'active_posts'   => fn () => Post::where('is_active', true)->get(),
        'has_active_post' => fn () => Post::where('is_active', true)->exists(),
        // ...
    ],
    // ...
]

```

---

## Available Functions

The following helper functions are available:
- `query(string $name): mixed`
- `getQuery(string $name): Closure(): mixed`

If you are using strict static analysis (e.g. PHPStan level max) or for autocompletion, you may prefer helpers that guarantee a specific return type.  
For that purpose, each helper is also available in a typed variant:
- Raw:
  - `queryInt(string $name): int`
  - `queryString(string $name): string`
  - `queryBool(string $name): bool`
  - `queryModel(string $name): Illuminate\Database\Eloquent\Model`
  - `queryCollection(string $name): Illuminate\Database\Eloquent\Collection`
- Lazy:
  - `getQueryBool(string $name): Closure(): bool`
  - `getQueryInt(string $name): Closure(): int`
  - `getQueryString(string $name): Closure(): string`
  - `getQueryModel(string $name): Closure(): Illuminate\Database\Eloquent\Model`
  - `getQueryCollection(string $name): Closure(): Illuminate\Database\Eloquent\Collection`

When working with route parameters or foreign keys, you often only need the model’s ID.  
For convenience, dedicated helpers are available:
- `queryId(string $name): int`
- `getQueryId(string $name): Closure(): int`

> [!TIP]
> You can easily build your own typed query helpers on top of the built-in ones.
>
> Examples:
> - `queryMyModel()` → wraps `queryModel()` but returns a specific model class
> - `queryMyCollection()` → wraps `queryCollection()` for a typed collection
> - `queryUuid()` → wraps `queryModel()` but returns a non-integer primary key

### Basic Usage

Using dynamic parameters in scenario:

```php
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertDatabaseHas;

Scenario::forApiRoute()->valid(
    // ...
    context: $context->withRouteParameters(['post' => getQueryId('post_first')]),
    // ...
    databaseAssertions: [
        fn () => assertDatabaseHas('posts', [
            'id' => queryId('post_first')
            // ...
        ])
    ]
);
```

### Advanced Usage

Create your own typed query helper:

```php
use App\Models\Post;
use function Jgss\LaravelPestScenarios\queryModel;

function queryPost(string $name): Post
{
    /** @var Post $post */
    $post = queryModel($name);

    return $post;
}
```
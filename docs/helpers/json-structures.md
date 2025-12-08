# Helper: JSON Structures

JSON structure helpers let you define reusable JSON structures referenced by a unique key.    
This is fully configurable in your config file [pest-scenarios.php](../../config/pest-scenarios.php).

### Minimal Example (TL;DR)

```php
use function Jgss\LaravelPestScenarios\jsonStructure;

jsonStructure('pagination');     // Reusable structure for paginated resources
```

---

## How It Works

JSON structures are resolved from the `resolvers.json_structures` section of your configuration.  
Each JSON structure key maps to a specific structure returned when using helpers.

Once resolved, a JSON structure can be:
- Returned immediately (`jsonStructure('pagination')`)
- Returned as a lazy closure (`getJsonStructure('pagination')`)

> [!NOTE]
> JSON structure helpers let you centralize and reuse common API response formats, keeping your tests consistent and reducing boilerplate across files.

---

## Configuration

JSON structures are defined in the `resolvers.json_structures` array in your `config/pest-scenarios.php`.  
By default, you will find the following configuration based on Laravel's formats:


```php
'resolvers' => [
    'json_structures' => [
        'resource' => ['data'],
        'pagination' => [
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => [
                'current_page', 'from', 'last_page',
                'links' => ['*' => ['url', 'label', 'active']],
            ],
        ],
        'token' => ['token'],
        'message' => ['message'],
        'none' => null,
    ],
    // ...
]
```

---

## Available Functions

The following helper functions are available:

- `jsonStructure(string $name): ?array`
- `getJsonStructure(string $name): Closure(): ?array`

### Basic Usage

Expecting a specific structure in a scenario:

```php
use Jgss\LaravelPestScenarios\Scenario;
use function Jgss\LaravelPestScenarios\getJsonStructure;

Scenario::forApiRoute()->valid(
    // ...
    expectedStructure: getJsonStructure('pagination'),
    // ...
);
```

### Advanced Usage

Using in a native Pest test:

```php
use function Jgss\LaravelPestScenarios\jsonStructure;

it('ensures whatever', function () {
    // ...
    
    $response->assertJsonStructure(jsonStructure('pagination'));    
})
```
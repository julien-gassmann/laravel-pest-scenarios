# Helper: Mock Factory

Mock Factory helpers provide a clean, convenient way to declare the `mocks` section inside a scenario Context.

### Minimal Example (TL;DR)

```php
use Illuminate\Notifications\Notification;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

makeMock(Notification::class, fn (MockInterface $mock) => $mock->shouldReceive('send')->once())
```

---

## How It Works

The `mocks` property of a Context requires a specific array format.  
`makeMock()` generates this structure for you in a clean and readable way. 

> [!NOTE]
> `makeMock()` returns a one-element associative array
> where the key is the class and the value is a closure returning the mock.  
> This makes it ready to be spread directly into the `mocks` property.

### When to Use

Use `makeMock()` whenever your Context requires mock definitions,  
but you want to avoid the verbosity of manual `Mockery::mock()` calls.

---

## Available Functions

The following helper function is available:

- `makeMock(string $class, Closure $definition): array`

### Without Helper

Declaring context's `mocks` property without using `makeMock()` helper:

```php
use Illuminate\Notifications\Notification;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

mocks: [
    Notification::class => fn () => Mockery::mock(Notification::class, function (MockInterface $mock) {
        $mock->shouldReceive('send')->once();
    }),
]
```

### With Helper

Declaring context's `mocks` property using `makeMock()` helper:

```php
use Illuminate\Notifications\Notification;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

// Context single mock
mocks: makeMock(Notification::class, fn (MockInterface $mock) => $mock->shouldReceive('send')->once()),

```

Because `makeMock()` returns a one-element associative array,
you can easily destructure multiple mocks inside the same context:

```php
use App\Service;
use App\OtherService;
use Jgss\LaravelPestScenarios\Context;
use Mockery\MockInterface;
use function Jgss\LaravelPestScenarios\makeMock;

// Context with multiple mocks
mocks: [
    ...makeMock(Service::class, fn (MockInterface $mock) => $mock->shouldReceive('method')),
    ...makeMock(OtherService::class, fn (MockInterface $mock) => $mock->shouldReceive('otherMethod')),
],
```
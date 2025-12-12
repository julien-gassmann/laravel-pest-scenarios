# Contributing to Laravel Pest Scenarios

First off, thank you for considering contributing!  
This package is still evolving, and every improvement, big or small, is appreciated.

You can help by:
- Fixing bugs
- Improving documentation
- Adding missing scenario features
- Enhancing test coverage
- Proposing small quality-of-life improvements

If you’re unsure whether your idea fits: just open an issue.


## How to Contribute

### 1. Fork the repository

Click “Fork” on GitHub and clone your fork locally:

```bash
git clone https://github.com/<your-username>/laravel-pest-scenarios.git
cd laravel-pest-scenarios
```

### (Optional) Using Docker

This repository includes a Docker setup for convenience.  
If you prefer working inside the container:

```bash
docker compose up -d
docker compose exec php bash
```

### 2. Install dependencies

```bash
composer install
```

### 3. Run the test suite

```bash
composer check
```

This runs:
- Rector
- Pint (linting)
- PHPStan (max)
- Parallel Unit + Feature tests with coverage (min=100)


### 4. Create a feature branch

Use a descriptive name:

```bash
git checkout -b feature/improve-context-api
```

### 5. Make your changes

Please keep:
- Code style consistent (Pint will handle most of it)
- Tests updated when necessary
- Backward compatibility in mind


### 6. Submit a pull request

Push your branch and open a PR on GitHub:

```bash
git push origin feature/improve-context-api
```

## Architectural Guidelines (for new features)

To keep the codebase consistent, please follow the patterns already used in the project.

- **Builders**: provide the public API (`valid()`, `invalid()`, `with()`) and define default values
(see [ApiRouteScenarioBuilder](src/Builders/Scenarios/ApiRouteScenarioBuilder.php)).

- **Scenario classes**: represent a single test case defining expectations and behavior 
(see [ValidApiRouteScenario](src/Definitions/Scenarios/ApiRoutes/ValidApiRouteScenario.php)).
  
- **Context classes**: store the shared state reused by every scenario in the test file 
(see [ApiRouteContext](src/Definitions/Contexts/ApiRouteContext.php)).
  
- **Context Traits**: no heavy logic, just data mutation (`withXxx()`) + accessors 
(see [HasRouteContext](src/Definitions/Contexts/Traits/HasRouteContext.php)).

- **Resolvers** : contain any non-trivial logic needed by Context traits 
(see [RouteResolver](src/Resolvers/Contexts/RouteResolver.php)).

If you add a new Scenario type:
- Provide a Builder
- Provide a Valid + Invalid version
- Implement `defineTest()`
- Use `PrepareContext` and call `prepareContext()` first

If you add a new Context type:
- Provide a Builder
- Keep only a constructor + `replicate()`
- Add one trait per property you introduce

If you’re unsure where to start, existing implementations offer good examples to build upon !

## PR Requirements

A pull request should:
- Pass all CI checks (tests, linting, static analysis)
- Include tests for any non-trivial change
- Update documentation if the public API changes

If you’re not sure how to test a feature, ask — we’ll help.

## Reporting Bugs

If you find something wrong:
- Open an issue 
- Include a reproduction if possible 
- Mention your PHP + Laravel + Pest versions

## Release Process (maintainers)

- Update version in composer.json
- Add relevant notes to the changelog (if present)
- Tag a new release:

```bash
git tag -a vX.Y.Z -m "Description"
git push origin vX.Y.Z
```


## Thank You

Every contribution matters, from typo fixes to new scenario types.  
Thank you for helping make Laravel testing cleaner and more enjoyable!
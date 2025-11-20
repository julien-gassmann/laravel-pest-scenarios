<?php

// use App\Http\Resources\RoleResource;
// use App\Http\Resources\UserResource;
// use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithConsoleEvents;
use Jgss\LaravelPestScenarios\Tests\TestCase;
use Laravel\Prompts\Prompt;

// use function Jgss\LaravelPestScenarios\actor;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->use(WithConsoleEvents::class)
    ->in('Feature', 'Unit');

// This allows prompt assertions
Prompt::fallbackWhen(true);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

// expect()->extend('toBeOne', function () {
//    return $this->toBe(1);
// });

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

// function expectedUserResource(string $actorName = 'guest'): Closure
// {
//    return fn () => UserResource::make(actor($actorName)?->load('roles'))->response();
// }
//
// function expectedRoleResource(string $name): Closure
// {
//    return fn () => RoleResource::make(Role::where('name', '=', $name)->firstOrFail())->response();
// }

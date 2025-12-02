<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery\MockInterface;
use PHPUnit\Framework\SkippedTestSuiteError;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\User;
use Workbench\App\Services\DummyService;

use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\actorId;
use function Jgss\LaravelPestScenarios\databaseSetup;
use function Jgss\LaravelPestScenarios\getActor;
use function Jgss\LaravelPestScenarios\getActorId;
use function Jgss\LaravelPestScenarios\getDatabaseSetup;
use function Jgss\LaravelPestScenarios\getJsonStructure;
use function Jgss\LaravelPestScenarios\getQuery;
use function Jgss\LaravelPestScenarios\getQueryBool;
use function Jgss\LaravelPestScenarios\getQueryCollection;
use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\getQueryInt;
use function Jgss\LaravelPestScenarios\getQueryModel;
use function Jgss\LaravelPestScenarios\getQueryString;
use function Jgss\LaravelPestScenarios\jsonStructure;
use function Jgss\LaravelPestScenarios\makeMock;
use function Jgss\LaravelPestScenarios\query;
use function Jgss\LaravelPestScenarios\queryBool;
use function Jgss\LaravelPestScenarios\queryCollection;
use function Jgss\LaravelPestScenarios\queryId;
use function Jgss\LaravelPestScenarios\queryInt;
use function Jgss\LaravelPestScenarios\queryModel;
use function Jgss\LaravelPestScenarios\queryString;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

/**
 * ───────────────────────────────────────
 * Valid scenarios for helper functions
 * ───────────────────────────────────────
 */
describe('Helpers : success', function (): void {

    // ------------------- Database setup -------------------

    it('creates the expected data in database', function (): void {
        // Arrange: Call makeMock helper
        databaseSetup('create_dummy');

        // Assert: Database has expected data
        assertDatabaseCount('dummies', 1);
        assertDatabaseHas('dummies', [
            'id' => 1,
            'email' => 'dummy@email.com',
        ]);
    });

    it('returns the expected value', function (array $dataset): void {
        // Arrange: Fill database
        databaseSetup('create_user');
        databaseSetup('create_dummy');

        // Arrange: Get dataset infos
        /** @var Closure $method */
        ['method' => $method, 'result' => $result] = $dataset;
        $result = is_callable($result) ? $result() : $result;

        // Assert: Helper returns expected result
        expect($method())->toEqual($result);
    })->with([
        // ------------------- Json Structures -------------------
        'jsonStructure' => [[
            'method' => fn (): ?array => jsonStructure('resource'),
            'result' => ['data'],
        ]],
        'getJsonStructure' => [[
            'method' => fn () => getJsonStructure('resource')(),
            'result' => ['data'],
        ]],
        // ------------------- Actors -------------------
        'actor' => [[
            'method' => fn (): ?Authenticatable => actor('user'),
            'result' => fn () => User::query()->firstOrFail(),
        ]],
        'getActor' => [[
            'method' => fn () => getActor('user')(),
            'result' => fn () => User::query()->firstOrFail(),
        ]],
        'actorId' => [[
            'method' => fn (): ?int => actorId('user'),
            'result' => fn () => User::query()->firstOrFail()->id,
        ]],
        'getActorId' => [[
            'method' => fn () => getActorId('user')(),
            'result' => fn () => User::query()->firstOrFail()->id,
        ]],
        // ------------------- Queries -------------------
        'query' => [[
            'method' => fn (): mixed => query('dummy_first'),
            'result' => fn () => Dummy::query()->firstOrFail(),
        ]],
        'getQuery' => [[
            'method' => fn () => getQuery('dummy_first')(),
            'result' => fn () => Dummy::query()->firstOrFail(),
        ]],
        'queryInt' => [[
            'method' => fn (): int => queryInt('int'),
            'result' => fn () => Dummy::query()->firstOrFail()->id,
        ]],
        'getQueryInt' => [[
            'method' => fn () => getQueryInt('int')(),
            'result' => fn () => Dummy::query()->firstOrFail()->id,
        ]],
        'queryString' => [[
            'method' => fn (): string => queryString('string'),
            'result' => fn () => Dummy::query()->firstOrFail()->name,
        ]],
        'getQueryString' => [[
            'method' => fn () => getQueryString('string')(),
            'result' => fn () => Dummy::query()->firstOrFail()->name,
        ]],
        'queryBool' => [[
            'method' => fn (): bool => queryBool('bool'),
            'result' => fn () => Dummy::query()->firstOrFail()->is_active,
        ]],
        'getQueryBool' => [[
            'method' => fn () => getQueryBool('bool')(),
            'result' => fn () => Dummy::query()->firstOrFail()->is_active,
        ]],
        'queryModel' => [[
            'method' => fn (): Model => queryModel('model'),
            'result' => fn () => Dummy::query()->firstOrFail(),
        ]],
        'getQueryModel' => [[
            'method' => fn () => getQueryModel('model')(),
            'result' => fn () => Dummy::query()->firstOrFail(),
        ]],
        'queryCollection' => [[
            'method' => fn (): Collection => queryCollection('collection'),
            'result' => fn () => Dummy::all(),
        ]],
        'getQueryCollection' => [[
            'method' => fn () => getQueryCollection('collection')(),
            'result' => fn () => Dummy::all(),
        ]],
        'queryId' => [[
            'method' => fn (): int => queryId('id'),
            'result' => fn () => Dummy::query()->firstOrFail()->id,
        ]],
        'getQueryId' => [[
            'method' => fn () => getQueryId('id')(),
            'result' => fn () => Dummy::query()->firstOrFail()->id,
        ]],
    ]);

    // ------------------- Mocks -------------------

    it('mocks the expected class with dataset "makeMock"', function (): void {
        // Arrange: Call makeMock helper
        $mockedClass = DummyService::class;
        $mockDefinition = fn (MockInterface $mock) => $mock->shouldHaveBeenCalled();
        $mock = makeMock($mockedClass, $mockDefinition);

        // Assert: Helper returns expected format
        expect($mock)->toEqual([$mockedClass => $mockDefinition]);
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for helper functions
 * ───────────────────────────────────────
 */
describe('Helpers : failure', function (): void {
    it('fails when using non-existent resolver key', function (): void {
        // Assert: Ensure correct SkippedTestSuiteError is thrown
        expect(getDatabaseSetup('non-existent'))
            ->toThrow(new SkippedTestSuiteError("Unknown resolver key 'non-existent' in 'resolvers.database_setups'."));
    });
});

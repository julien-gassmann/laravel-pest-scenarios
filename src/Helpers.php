<?php

/** @noinspection PhpUnused */

namespace Jgss\LaravelPestScenarios;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Jgss\LaravelPestScenarios\Resolvers\Config\ActorResolver;
use Jgss\LaravelPestScenarios\Resolvers\Config\DatabaseSetupResolver;
use Jgss\LaravelPestScenarios\Resolvers\Config\JsonStructureResolver;
use Jgss\LaravelPestScenarios\Resolvers\Config\QueryResolver;
use Mockery;
use Mockery\MockInterface;

// ------------------- Json Structures -------------------
/**
 * @return array<array-key, mixed>|null
 */
function jsonStructure(string $type): ?array
{
    return JsonStructureResolver::get($type);
}

/**
 * @return Closure(): (array<array-key, mixed>|null)
 */
function getJsonStructure(string $type): Closure
{
    return fn () => JsonStructureResolver::get($type);
}

// ------------------- Actors -------------------
function actor(string $actorName): ?User
{
    return ActorResolver::get($actorName);
}

/**
 * @return Closure(): ?User
 */
function getActor(string $actorName): Closure
{
    return fn () => ActorResolver::get($actorName);
}

function actorId(string $actorName): ?int
{
    return ActorResolver::getId($actorName);
}

/**
 * @return Closure(): ?int
 */
function getActorId(string $actorName): Closure
{
    return fn () => ActorResolver::getId($actorName);
}

// ------------------- Database setup -------------------
function databaseSetup(string $name): void
{
    DatabaseSetupResolver::get($name);
}

/**
 * @return Closure(): void
 */
function getDatabaseSetup(string $name): Closure
{
    return fn () => DatabaseSetupResolver::get($name);
}

// ------------------- Queries -------------------
function query(string $name): mixed
{
    return QueryResolver::get($name);
}

function queryInt(string $name): int
{
    /** @phpstan-ignore-next-line  */
    return intval(QueryResolver::get($name));
}

function queryString(string $name): string
{
    /** @phpstan-ignore-next-line  */
    return strval(QueryResolver::get($name));
}

function queryBool(string $name): bool
{
    return boolval(QueryResolver::get($name));
}

function queryModel(string $name): Model
{
    /** @var Model $model */
    $model = QueryResolver::get($name);

    return $model;
}

/**
 * @return Collection<array-key, Model>
 */
function queryCollection(string $name): Collection
{
    /** @var Collection<array-key, Model> $collection */
    $collection = QueryResolver::get($name);

    return $collection;
}

function queryId(string $name): int
{
    /** @var Model&object{id: int} $model */
    $model = QueryResolver::get($name);

    return $model->id;
}

/**
 * @return Closure(): mixed
 */
function getQuery(string $name): Closure
{
    return fn () => query($name);
}

/**
 * @return Closure(): int
 */
function getQueryInt(string $name): Closure
{
    return fn () => queryInt($name);
}

/**
 * @return Closure(): string
 */
function getQueryString(string $name): Closure
{
    return fn () => queryString($name);
}

/**
 * @return Closure(): bool
 */
function getQueryBool(string $name): Closure
{
    return fn () => queryBool($name);
}

/**
 * @return Closure(): Model
 */
function getQueryModel(string $name): Closure
{
    return fn () => queryModel($name);
}

/**
 * @return Closure(): Collection<array-key, Model>
 */
function getQueryCollection(string $name): Closure
{
    return fn () => queryCollection($name);
}

/**
 * @return Closure(): int
 */
function getQueryId(string $name): Closure
{
    return fn () => queryId($name);
}

// ------------------- Queries -------------------

/**
 * @param  class-string  $class
 * @return non-empty-array<class-string, Closure(): MockInterface>
 */
function makeMock(string $class, Closure $definition): array
{
    return [$class => fn () => Mockery::mock($class, $definition)];
}

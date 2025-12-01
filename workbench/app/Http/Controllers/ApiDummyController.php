<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ValidatedInput;
use Workbench\App\Http\Requests\DummyQueryRequest;
use Workbench\App\Http\Requests\DummyRequest;
use Workbench\App\Http\Resources\DummyResource;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\DummyChild;

class ApiDummyController
{
    /**
     * @return AnonymousResourceCollection<LengthAwarePaginator<array-key, DummyResource>>
     */
    public function index(DummyQueryRequest $request): AnonymousResourceCollection
    {
        /** @var int $page */
        $page = $request->validated('page') ?? 1;
        /** @var int $perPage */
        $perPage = $request->validated('perPage') ?? 5;
        /** @var string $sort */
        $sort = $request->validated('sort') ?? 'id';

        $dummiesPaginated = Dummy::query()
            ->orderBy($sort)
            ->paginate(perPage: $perPage, page: $page);

        return DummyResource::collection($dummiesPaginated);
    }

    public function show(Dummy $dummy): DummyResource
    {
        return DummyResource::make($dummy->load('children'));
    }

    public function store(DummyRequest $request): DummyResource
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $dummy = Dummy::query()->create($data);

        return DummyResource::make($dummy);
    }

    public function update(DummyRequest $request, Dummy $dummy): DummyResource
    {
        /** @var int[]|null $children */
        $children = $request->validated('children_ids');

        /** @var ValidatedInput $safe */
        $safe = $request->safe();
        /** @var array<string, mixed> $data */
        $data = $safe->except(['children_ids']);
        $dummy->update($data);

        if (! is_null($children)) {
            DummyChild::query()->where('dummy_id', $dummy->id)->update(['dummy_id' => null]);
            DummyChild::query()->whereIn('id', $children)->update(['dummy_id' => $dummy->id]);
        }

        return DummyResource::make($dummy->load('children'));
    }

    public function destroy(Dummy $dummy): JsonResponse
    {
        $dummy->children()->delete();
        $dummy->delete();

        return response()->json(['message' => 'Dummy deleted']);
    }
}

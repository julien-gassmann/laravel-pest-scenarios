<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\ValidatedInput;
use Workbench\App\Http\Requests\DummyQueryRequest;
use Workbench\App\Http\Requests\DummyRequest;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\DummyChild;

class WebDummyController
{
    public function index(DummyQueryRequest $request): View
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

        return view('workbench::index', ['dummiesPaginated' => $dummiesPaginated]);
    }

    public function show(Dummy $dummy): View
    {
        $dummy->load('children');

        return view('workbench::show', ['dummy' => $dummy]);
    }

    public function store(DummyRequest $request): View
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $dummy = Dummy::query()->create($data);

        return view('workbench::show', ['dummy' => $dummy]);
    }

    public function update(DummyRequest $request, Dummy $dummy): View
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

        $dummy->load('children');

        return view('workbench::show', ['dummy' => $dummy]);
    }

    public function destroy(Dummy $dummy): View
    {
        $dummy->children()->delete();
        $dummy->delete();
        $dummiesPaginated = Dummy::query()->paginate(5);

        return view('workbench::index', ['dummiesPaginated' => $dummiesPaginated]);
    }
}

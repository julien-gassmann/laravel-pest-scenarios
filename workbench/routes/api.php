<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Enums\DummyEnum;
use Workbench\App\Http\Controllers\ApiDummyController;
use Workbench\App\Http\Middleware\DummyMiddleware;
use Workbench\App\Http\Requests\RouteBindingRequest;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\DummyChild;
use Workbench\App\Services\DummyService;

Route::middleware(['api', 'auth'])
    ->prefix('api')
    ->group(function (): void {
        Route::get('/dummies', [ApiDummyController::class, 'index'])->name('api.dummies.index');
        Route::post('/dummies', [ApiDummyController::class, 'store'])->name('api.dummies.store');
        Route::middleware(DummyMiddleware::class)->group(function (): void {
            Route::get('/dummies/{dummy}', [ApiDummyController::class, 'show'])->name('api.dummies.show');
            Route::patch('/dummies/{dummy}', [ApiDummyController::class, 'update'])->name('api.dummies.update');
            Route::put('/dummies/{dummy}', [ApiDummyController::class, 'update'])->name('api.put.dummies.update');
            Route::delete('/dummies/{dummy}', [ApiDummyController::class, 'destroy'])->name('api.dummies.delete');
        });
    });

Route::prefix('api')
    ->group(function (): void {
        Route::get('/multiple/{dummy}/bindings/{dummyChild}', fn (RouteBindingRequest $request, Dummy $dummy, DummyChild $dummyChild): null => null)
            ->name('api.multiple.bindings');
        Route::get('/model/{dummy:email}', fn (RouteBindingRequest $request, Dummy $dummy): null => null)
            ->name('api.model.column.binding');
        Route::get('/built-in/{int}', fn (RouteBindingRequest $request, int $int): null => null)
            ->name('api.built.in.binding');
        Route::get('/class/{class}', fn (RouteBindingRequest $request, DummyService $class): null => null)
            ->name('api.class.binding');
        Route::get('/enum/{enum}', fn (RouteBindingRequest $request, DummyEnum $enum): null => null)
            ->name('api.enum.binding');
    });

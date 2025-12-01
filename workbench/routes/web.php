<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\WebDummyController;
use Workbench\App\Http\Middleware\DummyMiddleware;

Route::get('/login', fn () => view('workbench::login'))->name('login');

Route::middleware(['web', 'auth'])
    ->prefix('web')
    ->group(function () {
        Route::get('/dummies', [WebDummyController::class, 'index'])->name('web.dummies.index');
        Route::post('/dummies', [WebDummyController::class, 'store'])->name('web.dummies.store');
        Route::middleware(DummyMiddleware::class)->group(function () {
            Route::get('/dummies/{dummy}', [WebDummyController::class, 'show'])->name('web.dummies.show');
            Route::patch('/dummies/{dummy}', [WebDummyController::class, 'update'])->name('web.dummies.update');
            Route::put('/dummies/{dummy}', [WebDummyController::class, 'update'])->name('web.put.dummies.update');
            Route::delete('/dummies/{dummy}', [WebDummyController::class, 'destroy'])->name('web.dummies.delete');
        });
    });

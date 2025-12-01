<?php

namespace Workbench\App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Throwable;

class WorkbenchExceptionHandler extends BaseHandler
{
    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return $request->is('api/*');
    }
}

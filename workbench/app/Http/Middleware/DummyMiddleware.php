<?php

namespace Workbench\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Workbench\App\Models\User;

class DummyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ?User $authUser */
        $authUser = $request->user();

        if ($authUser?->name === 'Unauthorized') {
            throw new AccessDeniedHttpException('Not Allowed.');
        }

        return $next($request);
    }
}
